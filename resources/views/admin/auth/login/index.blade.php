<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - MCX Classic Bullion</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        body {
            background-color: var(--bg-main);
            margin: 0;
            font-family: 'Inter', sans-serif;
        }
        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: var(--primary);
            border-radius: 0;
            padding: 48px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            text-align: center;
        }
        .login-card h1 {
            color: var(--accent);
            font-weight: 800;
            font-size: 1.8rem;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .login-card p {
            color: rgba(255,255,255,0.7);
            margin-bottom: 40px;
            font-size: 0.9rem;
        }
        .form-group {
            text-align: left;
            margin-bottom: 24px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: #ffffff;
            padding: 14px;
            border-radius: 4px;
            outline: none;
            box-sizing: border-box;
        }
        .form-group input:focus {
            border-color: var(--accent);
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <h1>MCX ADMIN</h1>
            <p>CLASSIC BULLION TERMINAL</p>
            
            <form action="{{ url('/admin/dashboard') }}">
                <div class="form-group">
                    <label>ACCESS EMAIL</label>
                    <input type="email" placeholder="admin@classicbullion.com" required>
                </div>
                <div class="form-group">
                    <label>PASSWORD</label>
                    <input type="password" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn btn-accent" style="width: 100%; height: 50px; font-weight: 700; margin-top: 16px;">AUTHORIZE ACCESS</button>
            </form>
            
            <div style="margin-top: 40px; font-size: 0.75rem; color: rgba(255,255,255,0.4);">
                SECURE TRADING ENVIRONMENT &copy; {{ date('Y') }}
            </div>
        </div>
    </div>
</body>
</html>
