param(
    [string] $BaseUrl = 'http://127.0.0.1:8000',
    [int] $SlowThresholdMs = 1000,
    [int] $TimeoutSeconds = 20,
    [string] $CookieHeader = $env:APP_PERF_COOKIE,
    [string] $LoginUsername
)

$ErrorActionPreference = 'Stop'
$routeJson = & php artisan route:list --json
if ($LASTEXITCODE -ne 0) {
    throw 'php artisan route:list --json gagal dijalankan.'
}

$routes = ($routeJson -join "`n") | ConvertFrom-Json
$handler = [System.Net.Http.HttpClientHandler]::new()
$handler.AllowAutoRedirect = $false
if (-not $CookieHeader) {
    $handler.UseCookies = $true
    $handler.CookieContainer = [System.Net.CookieContainer]::new()
}
$client = [System.Net.Http.HttpClient]::new($handler)
$client.Timeout = [TimeSpan]::FromSeconds($TimeoutSeconds)
if ($CookieHeader) {
    $client.DefaultRequestHeaders.TryAddWithoutValidation('Cookie', $CookieHeader) | Out-Null
}

$results = [System.Collections.Generic.List[object]]::new()
$skipped = 0
$base = $BaseUrl.TrimEnd('/')
$authenticated = [bool] $CookieHeader

if ($LoginUsername) {
    $securePassword = Read-Host "Password untuk $LoginUsername" -AsSecureString
    $password = [System.Net.NetworkCredential]::new('', $securePassword).Password

    try {
        $loginHtml = $client.GetStringAsync("$base/login").GetAwaiter().GetResult()
        $tokenMatch = [regex]::Match($loginHtml, '<input[^>]+name="_token"[^>]+value="([^"]+)"')
        if (-not $tokenMatch.Success) {
            throw 'Token CSRF tidak ditemukan di halaman login.'
        }

        $form = [System.Collections.Generic.Dictionary[string, string]]::new()
        $form.Add('_token', [System.Net.WebUtility]::HtmlDecode($tokenMatch.Groups[1].Value))
        $form.Add('username', $LoginUsername)
        $form.Add('password', $password)
        $content = [System.Net.Http.FormUrlEncodedContent]::new($form)
        $loginResponse = $client.PostAsync("$base/login", $content).GetAwaiter().GetResult()
        $loginLocation = [string] $loginResponse.Headers.Location
        $loginResponse.Dispose()
        $content.Dispose()
        $password = $null

        $adminResponse = $client.GetAsync("$base/admin", [System.Net.Http.HttpCompletionOption]::ResponseHeadersRead).GetAwaiter().GetResult()
        $authenticated = $adminResponse.IsSuccessStatusCode
        $adminResponse.Dispose()
        if (-not $authenticated) {
            throw 'Login gagal atau akun tidak dapat mengakses panel admin.'
        }
    } finally {
        $securePassword.Dispose()
        $password = $null
    }
}

foreach ($route in $routes) {
    if ($route.method -notmatch '(^|\|)GET(\||$)|(^|\|)HEAD(\||$)') {
        $skipped++
        continue
    }

    $uri = [string] $route.uri
    if ($uri -match '(^|/)logout($|/)|filament/exports|filament/imports|\{path\}') {
        $skipped++
        continue
    }

    $path = [regex]::Replace($uri, '\{[^}/]+\?\}', '1')
    $path = [regex]::Replace($path, '\{[^}/]+\}', '1')
    $path = $path.TrimStart('/')
    $url = if ($path) { "$base/$path" } else { "$base/" }
    $watch = [System.Diagnostics.Stopwatch]::StartNew()
    $status = 'ERROR'
    $detail = ''

    try {
        $response = $client.GetAsync($url, [System.Net.Http.HttpCompletionOption]::ResponseHeadersRead).GetAwaiter().GetResult()
        $watch.Stop()
        $status = [int] $response.StatusCode
        if ($status -ge 300 -and $status -lt 400) {
            $detail = 'redirect ' + [string] $response.Headers.Location
        }
        $response.Dispose()
    } catch {
        $watch.Stop()
        $detail = $_.Exception.GetBaseException().Message
    }

    $results.Add([pscustomobject] @{
        Ms = $watch.ElapsedMilliseconds
        Status = $status
        Method = 'GET'
        Uri = if ($path) { "/$path" } else { "/" }
        Name = $route.name
        Action = $route.action
        Detail = $detail
    })
}

$client.Dispose()

Write-Output "Base URL: $base"
Write-Output "Authenticated session: $authenticated"
Write-Output "Routes measured: $($results.Count); skipped (non-GET or unsafe download/logout): $skipped"
Write-Output "Slow threshold: ${SlowThresholdMs}ms"
Write-Output 'HTTP status counts:'
$results | Group-Object Status | Sort-Object Name | ForEach-Object {
    Write-Output "  $($_.Name): $($_.Count)"
}
Write-Output ''
Write-Output 'Slow routes:'
$slow = @($results | Where-Object { $_.Ms -ge $SlowThresholdMs } | Sort-Object Ms -Descending)
if ($slow.Count -eq 0) {
    Write-Output '  None above threshold.'
} else {
    $slow | Format-Table Ms, Status, Uri, Name, Action, Detail -Wrap -AutoSize | Out-String -Width 240 | Write-Output
}

Write-Output 'Top 10 route timings:'
$results | Sort-Object Ms -Descending | Select-Object -First 10 | Format-Table Ms, Status, Uri, Name, Detail -Wrap -AutoSize | Out-String -Width 200 | Write-Output

$redirectCount = @($results | Where-Object { $_.Status -eq 302 -or $_.Status -eq 301 }).Count
if (-not $authenticated -and $redirectCount -gt 0) {
    Write-Output "Note: $redirectCount route(s) redirected; protected admin timings measure the login gate. Run with -LoginUsername or set APP_PERF_COOKIE to measure rendered admin pages."
}
