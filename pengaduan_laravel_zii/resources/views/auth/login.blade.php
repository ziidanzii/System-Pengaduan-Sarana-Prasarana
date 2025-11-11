<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sarpras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --secondary: #64748b;
            --light-bg: #f8fafc;
            --text-dark: #0f172a;
            --border: #e2e8f0;
        }

        body {
            background: linear-gradient(135deg, #ebf2ff, #f8fafc);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 1rem;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.1);
            padding: 2.5rem;
            animation: fadeInUp 0.8s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-container {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }

        .logo-container i {
            font-size: 2.5rem;
            color: white;
        }

        .login-title {
            color: var(--text-dark);
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.25rem;
        }

        .login-subtitle {
            color: var(--secondary);
            font-size: 0.95rem;
        }

        .form-label {
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control {
            background: white;
            border: 2px solid var(--border);
            border-radius: 10px;
            color: var(--text-dark);
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary);
            z-index: 5;
        }

        .input-with-icon {
            padding-left: 3rem;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            padding: 0.75rem 2rem;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3);
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }

        .register-link {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 500;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        .alert-danger, .alert-success {
            border-radius: 10px;
            font-size: 0.9rem;
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--secondary);
            cursor: pointer;
            z-index: 5;
        }

        .password-toggle:hover {
            color: var(--primary-dark);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 18px;
            height: 18px;
            margin-top: -9px;
            margin-left: -9px;
            border: 2px solid white;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <i class="fas fa-school"></i>
                </div>
                <h1 class="login-title">Login Sarpras</h1>
                <p class="login-subtitle">Masuk sistem sarana & prasarana</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    @foreach ($errors->all() as $error)
                        {{ $error }}@if(!$loop->last)<br>@endif
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                @csrf
                <div class="mb-3">
                    <label for="login" class="form-label">Email atau Username</label>
                    <div class="position-relative">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="login" id="login"
                            class="form-control input-with-icon"
                            placeholder="Masukkan email atau username"
                            value="{{ old('login') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="position-relative">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password"
                            class="form-control input-with-icon"
                            placeholder="Masukkan password" required>
                        <button type="button" class="password-toggle" id="passwordToggle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-login" id="loginButton">
                    <i class="fas fa-sign-in-alt me-2"></i>Masuk
                </button>
            </form>

            <div class="login-footer">
                <a href="{{ route('register') }}" class="register-link">
                    <i class="fas fa-user-plus me-1"></i>Belum punya akun? Daftar di sini
                </a>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('passwordToggle').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        // Loading animation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginButton');
            if (btn.disabled) {
                e.preventDefault();
                return;
            }
            btn.classList.add('btn-loading');
            btn.disabled = true;
            setTimeout(() => {
                btn.classList.remove('btn-loading');
                btn.disabled = false;
            }, 10000);
        });
    </script>

</body>
</html>
