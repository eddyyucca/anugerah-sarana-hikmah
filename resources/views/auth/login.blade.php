<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Workshop ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body{
    background: url("{{ asset('assets/images/bg.png') }}") no-repeat center center;
    background-size: cover;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: Inter, "Segoe UI", Arial, sans-serif;
}
        .login-card{
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(8px);
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0,0,0,.3);
    width: 100%;
    max-width: 420px;
    padding: 2.5rem;
}
        .login-logo {
    height: 120px;
    width: auto;
    max-width: 180px;
    object-fit: contain;
    display: block;
    margin: 0 auto 1rem;
    background: transparent;
    border: none;
    border-radius: 0;
    box-shadow: none;
}
        .form-control { border-radius: 12px; padding: .75rem 1rem; border: 2px solid #e5e7eb; }
        .form-control:focus { border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220,38,38,.1); }
        .btn-login { background: linear-gradient(135deg, #dc2626, #ef4444); border: none; border-radius: 12px; padding: .8rem; font-weight: 700; font-size: 1rem; color: #fff; width: 100%; box-shadow: 0 6px 20px rgba(220,38,38,.3); }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(220,38,38,.4); color: #fff; }
    </style>
</head>
<body>
    <div class="login-card">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Workshop ERP Logo" class="login-logo">
        <h4 class="text-center mb-1" style="font-weight:800;">Workshop ERP</h4>
        <p class="text-center text-muted mb-4" style="font-size:.88rem;">Mining Logistics System</p>

        @if($errors->any())
        <div class="alert alert-danger py-2" style="border-radius:12px;font-size:.88rem;">
            <i class="bi bi-exclamation-triangle me-1"></i> {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold" style="font-size:.88rem;">Email</label>
                <div class="input-group">
                    <span class="input-group-text" style="border-radius:12px 0 0 12px;border:2px solid #e5e7eb;border-right:0;background:#f9fafb;"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="admin@workshop.local" required autofocus style="border-left:0;">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold" style="font-size:.88rem;">Password</label>
                <div class="input-group">
                    <span class="input-group-text" style="border-radius:12px 0 0 12px;border:2px solid #e5e7eb;border-right:0;background:#f9fafb;"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Password" required style="border-left:0;">
                </div>
            </div>
            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                <label class="form-check-label" for="remember" style="font-size:.85rem;">Remember me</label>
            </div>
            <button type="submit" class="btn-login"><i class="bi bi-box-arrow-in-right me-2"></i>Login</button>
        </form>

        <!-- <div class="text-center mt-3 text-muted" style="font-size:.75rem;">Default: admin@workshop.local / password</div> -->
    </div>
</body>
</html>
