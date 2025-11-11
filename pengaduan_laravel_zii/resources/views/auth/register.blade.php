<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | Sistem Sarpras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* --- Variabel & Gaya Global --- */
        :root {
            --color-primary: #3b82f6;
            --color-primary-dark: #2563eb;
            --color-neutral: #64748b;
            --color-text: #1e293b;
            --color-bg-light: #f1f5f9;
            --color-border: #cbd5e1;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--color-bg-light);
            display: grid;
            place-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* --- Layout Utama --- */
        .auth-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.07);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- Header & Logo --- */
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .icon-wrapper {
            background-color: var(--color-primary);
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(14, 165, 233, 0.35);
        }

        .icon-wrapper i {
            color: white;
            font-size: 2rem;
        }

        .icon-wrapper::after {
            content: '+';
            position: absolute;
            bottom: -5px;
            right: -5px;
            background-color: white;
            color: var(--color-primary);
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .auth-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--color-text);
            margin: 0;
        }

        .auth-subtitle {
            color: var(--color-neutral);
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        /* --- Komponen Form --- */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--color-text);
            margin-bottom: 0.5rem;
            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.8rem;
            border: 1.5px solid var(--color-border);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.2s ease-in-out;
            background-repeat: no-repeat;
            background-position: 12px center;
            background-size: 18px;
        }

        /* Ikon sebagai background-image */
        .form-input[name="nama_pengguna"] { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2364748b'%3E%3Cpath d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E"); }
        .form-input[name="username"] { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2364748b'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z'/%3E%3C/svg%3E"); }
        .form-input[name="email"] { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2364748b'%3E%3Cpath d='M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z'/%3E%3C/svg%3E"); }
        .form-input[name="password"], .form-input[name="password_confirmation"] { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2364748b'%3E%3Cpath d='M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z'/%3E%3C/svg%3E"); }

        .form-input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--color-neutral);
            cursor: pointer;
            padding: 5px;
        }

        .toggle-password:hover {
            color: var(--color-primary);
        }

        /* --- Tombol & Footer --- */
        .btn-submit {
            background-color: var(--color-primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            padding: 0.8rem;
            margin-top: 1.5rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-submit:hover {
            background-color: var(--color-primary-dark);
        }

        .btn-submit.is-loading {
            color: transparent;
            pointer-events: none;
        }

        .btn-submit.is-loading::after {
            content: "";
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid white;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spinner 0.8s linear infinite;
        }

        @keyframes spinner {
            to { transform: rotate(360deg); }
        }

        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--color-border);
            font-size: 0.9rem;
            color: var(--color-neutral);
        }

        .auth-footer a {
            color: var(--color-primary-dark);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 0.8rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .alert-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    </style>
</head>
<body>

<main class="auth-card">
    <header class="auth-header">
        <div class="icon-wrapper">
            <i class="fas fa-user"></i>
        </div>
        <h1 class="auth-title">Buat Akun Baru</h1>
        <p class="auth-subtitle">Bergabung dengan Sistem Sarpras</p>
    </header>

    {{-- Blade Directives for Error/Success Handling --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i> Mohon perbaiki kesalahan berikut:
            <ul class="mt-2 mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <form id="registrationForm" method="POST" action="{{ route('register.post') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="nama_pengguna">Nama Lengkap</label>
            <div class="input-wrapper">
                <input type="text" id="nama_pengguna" name="nama_pengguna" class="form-input" placeholder="Masukkan nama lengkap" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <div class="input-wrapper">
                <input type="text" id="username" name="username" class="form-input" placeholder="Pilih username unik" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <div class="input-wrapper">
                <input type="email" id="email" name="email" class="form-input" placeholder="nama@email.com" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <div class="input-wrapper">
                <input type="password" id="password" name="password" class="form-input" placeholder="Buat password" required>
                <button type="button" class="toggle-password" title="Tampilkan password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
            <div class="input-wrapper">
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Ketik ulang password" required>
                <button type="button" class="toggle-password" title="Tampilkan password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">
            Daftar Sekarang
        </button>
    </form>

    <footer class="auth-footer">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </footer>
</main>

<script>
    // --- Fungsi Toggle Password (Lebih Generik) ---
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // --- Fungsi Loading Saat Submit ---
    const form = document.getElementById('registrationForm');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', function() {
        submitBtn.classList.add('is-loading');
        submitBtn.disabled = true;

    });
</script>

</body>
</html>
