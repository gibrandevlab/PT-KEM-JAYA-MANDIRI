<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeFeature
{
    public function handle(Request $request, Closure $next, string $feature, string $action = 'view'): Response
    {
        abort_unless($request->user()?->hasAccess($feature, $action), 403);

        return $next($request);
    }
}
