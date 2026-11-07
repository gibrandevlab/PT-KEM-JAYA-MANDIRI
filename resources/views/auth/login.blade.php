<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - KJM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --bg: #0a0f1e;
      --surface: #111827;
      --surface2: #1f2937;
      --primary: #f97316;
      --primary-dark: #ea6c0a;
      --primary-glow: rgba(249, 115, 22, 0.3);
      --text: #f9fafb;
      --text-muted: #9ca3af;
      --border: #374151;
      --success: #10b981;
      --danger: #ef4444;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    body::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse at 20% 50%, rgba(249, 115, 22, 0.12) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(59, 130, 246, 0.08) 0%, transparent 50%);
      pointer-events: none;
    }

    .particles {
      position: absolute;
      inset: 0;
      overflow: hidden;
      pointer-events: none;
    }

    .particle {
      position: absolute;
      width: 2px;
      height: 2px;
      background: var(--primary);
      border-radius: 50%;
      animation: float linear infinite;
      opacity: 0;
    }

    @keyframes float {
      0% {
        transform: translateY(100vh) rotate(0deg);
        opacity: 0;
      }

      10% {
        opacity: 0.6;
      }

      90% {
        opacity: 0.3;
      }

      100% {
        transform: translateY(-20px) rotate(720deg);
        opacity: 0;
      }
    }

    .login-container {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 440px;
      padding: 0 20px;
    }

    .login-card {
      background: linear-gradient(135deg, rgba(31, 41, 55, 0.95), rgba(17, 24, 39, 0.98));
      border: 1px solid var(--border);
      border-radius: 24px;
      padding: 48px 40px;
      backdrop-filter: blur(20px);
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(249, 115, 22, 0.1);
      animation: slideUp 0.6s ease;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .login-logo {
      text-align: center;
      margin-bottom: 32px;
    }

    .logo-icon {
      width: 72px;
      height: 72px;
      background: linear-gradient(135deg, var(--primary), #fb923c);
      border-radius: 20px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 16px;
      box-shadow: 0 8px 24px var(--primary-glow);
      animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {

      0%,
      100% {
        box-shadow: 0 8px 24px rgba(249, 115, 22, 0.3);
      }

      50% {
        box-shadow: 0 8px 32px rgba(249, 115, 22, 0.6);
      }
    }

    .logo-icon i {
      font-size: 32px;
      color: white;
    }

    .login-logo h1 {
      font-size: 22px;
      font-weight: 800;
      color: var(--text);
      letter-spacing: -0.5px;
    }

    .login-logo p {
      font-size: 13px;
      color: var(--text-muted);
      margin-top: 4px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: var(--text-muted);
      margin-bottom: 8px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .input-wrap {
      position: relative;
    }

    .input-wrap i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      font-size: 16px;
    }

    .form-control {
      width: 100%;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 12px 16px 12px 44px;
      color: var(--text);
      font-size: 15px;
      font-family: 'Inter', sans-serif;
      transition: all 0.3s;
      outline: none;
    }

    .form-control::placeholder {
      color: #4b5563;
    }

    .form-control:focus {
      border-color: var(--primary);
      background: rgba(249, 115, 22, 0.05);
      box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
    }

    .btn-login {
      width: 100%;
      background: linear-gradient(135deg, var(--primary), #fb923c);
      border: none;
      border-radius: 12px;
      padding: 14px;
      color: white;
      font-size: 16px;
      font-weight: 700;
      font-family: 'Inter', sans-serif;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 8px;
      letter-spacing: 0.5px;
    }

    .remember-row {
      display: flex;
      align-items: center;
      gap: 9px;
      margin: 2px 0 12px;
      color: var(--text-muted);
      font-size: 13px;
    }

    .remember-row input { accent-color: var(--primary); }

    .password-toggle {
      position: absolute;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      border: none;
      outline: none;
      background: transparent;
      color: var(--text-muted);
      cursor: pointer;
      padding: 0;
      width: 32px;
      height: 32px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
      font-size: 15px;
      line-height: 1;
      transition: color 0.2s, background 0.2s;
      flex-shrink: 0;
    }

    .password-toggle:hover {
      color: var(--primary);
      background: rgba(249, 115, 22, 0.12);
    }

    .password-toggle:focus-visible {
      color: var(--primary);
      background: rgba(249, 115, 22, 0.12);
      box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.35);
    }

    .password-input { padding-right: 50px; }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px var(--primary-glow);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .alert-error {
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.3);
      border-radius: 10px;
      padding: 12px 16px;
      color: #fca5a5;
      font-size: 14px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .login-footer {
      text-align: center;
      margin-top: 24px;
      color: var(--text-muted);
      font-size: 12px;
    }
  </style>
</head>

<body>
  <div class="particles" id="particles"></div>
  <div class="login-container">
    <div class="login-card">
      <div class="login-logo">
        <div class="logo-icon" style="background:transparent; box-shadow:none; width:auto; height:90px; margin-bottom:20px; animation:none;">
          <img src="{{ asset('assets/logo.png') }}" alt="Logo KJM" style="height:100%; width:auto;">
        </div>
        <h1>KJM</h1>
        <p>PT KEM JAYA MANDIRI</p>
      </div>

      @if (isset($errors) && $errors->any())
        <div class="alert-error">
          <i class="fas fa-circle-exclamation"></i>
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('login.post') }}" autocomplete="on">
        @csrf
        <div class="form-group">
          <label class="form-label">Username / Email</label>
          <div class="input-wrap">
            <i class="fas fa-user"></i>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="{{ old('username') }}" autocomplete="username" required autofocus>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-wrap">
            <i class="fas fa-lock"></i>
            <input id="password" type="password" name="password" class="form-control password-input" placeholder="Masukkan password" autocomplete="current-password" required>
            <button class="password-toggle" type="button" id="toggle-password" aria-label="Tampilkan password" aria-pressed="false">
              <i class="fas fa-eye" aria-hidden="true"></i>
            </button>
          </div>
        </div>
        <label class="remember-row">
          <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
          <span>Remember Me</span>
        </label>
        <button type="submit" class="btn-login"><i class="fas fa-right-to-bracket"></i> &nbsp;Masuk</button>
      </form>
      <div class="login-footer">&copy; 2026 PT KEM JAYA MANDIRI. All rights reserved.</div>
    </div>
  </div>
  <script>
    const passwordInput = document.getElementById('password');
    const passwordToggle = document.getElementById('toggle-password');
    passwordToggle.addEventListener('click', () => {
      const showPassword = passwordInput.type === 'password';
      passwordInput.type = showPassword ? 'text' : 'password';
      passwordToggle.setAttribute('aria-label', showPassword ? 'Sembunyikan password' : 'Tampilkan password');
      passwordToggle.setAttribute('aria-pressed', String(showPassword));
      passwordToggle.querySelector('i').className = showPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
    });

    const container = document.getElementById('particles');
    for (let i = 0; i < 25; i++) {
      const p = document.createElement('div');
      p.className = 'particle';
      p.style.cssText = `left:${Math.random()*100}%;width:${Math.random()*3+1}px;height:${Math.random()*3+1}px;animation-duration:${Math.random()*15+8}s;animation-delay:${Math.random()*10}s;opacity:0`;
      container.appendChild(p);
    }
  </script>
</body>

</html>
