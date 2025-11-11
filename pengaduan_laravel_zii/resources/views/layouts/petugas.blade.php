<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petugas - @yield('title', 'Dashboard')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --primary-50: #eef2ff;
            --secondary: #64748b;
            --accent: #f59e0b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --light: #ffffff;
            --light-bg: #f6f8fb;
            --light-card: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --border-light: #f1f5f9;
            --shadow: 0 8px 24px rgba(2, 6, 23, 0.08);
            --shadow-lg: 0 16px 36px rgba(2, 6, 23, 0.12);
        }

        html.dark {
            --light: #0b1220;
            --light-bg: #080f1d;
            --light-card: #0f172a;
            --text-primary: #e5e7eb;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border: #1f2937;
            --border-light: #0b1220;
            --shadow: 0 8px 24px rgba(0,0,0,0.35);
            --shadow-lg: 0 16px 36px rgba(0,0,0,0.45);
        }

        body {
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(1200px 600px at -10% -10%, rgba(37, 99, 235, 0.07), transparent 50%),
                radial-gradient(1000px 500px at 110% -20%, rgba(16, 185, 129, 0.06), transparent 50%),
                radial-gradient(800px 400px at 50% 120%, rgba(245, 158, 11, 0.06), transparent 50%),
                var(--light-bg);
            min-height: 100vh;
            color: var(--text-primary);
        }

        /* Navbar Styling */
        .navbar-custom {
            background: rgba(255,255,255,0.6);
            border-bottom: 1px solid var(--border);
            padding: 0.85rem 0;
            box-shadow: var(--shadow);
            backdrop-filter: saturate(1.2) blur(10px);
        }
        html.dark .navbar-custom { background: rgba(15, 23, 42, 0.65); }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--text-primary) !important;
            display: flex;
            align-items: center;
        }

        .navbar-brand i {
            color: var(--primary);
            background: var(--primary-50);
            padding: 0.5rem;
            border-radius: 12px;
            margin-right: 0.75rem;
        }

        .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 500;
            padding: 0.75rem 1.25rem;
            margin: 0 0.2rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
        }

        .nav-link:hover {
            color: var(--primary) !important;
            background: var(--primary-50);
            transform: translateY(-1px);
        }

        .nav-link.active {
            color: var(--primary) !important;
            background: var(--primary-50);
            font-weight: 600;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--primary);
            border-radius: 2px;
        }

        .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        /* Theme Toggle */
        .theme-toggle {
            border: 1px solid var(--border);
            background: var(--light);
            color: var(--text-primary);
            border-radius: 10px;
            padding: 0.5rem 0.75rem;
        }
        .theme-toggle:hover {
            background: var(--primary-50);
            color: var(--primary);
        }

        /* User Dropdown */
        .user-dropdown .dropdown-toggle {
            color: var(--text-primary) !important;
            background: transparent;
            border: none;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .user-dropdown .dropdown-toggle:hover {
            background: var(--primary-50);
            color: var(--primary) !important;
        }

        .user-dropdown .dropdown-toggle::after {
            margin-left: 0.5rem;
        }

        .user-dropdown .dropdown-menu {
            background: var(--light);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            padding: 0.5rem;
            min-width: 200px;
        }

        .user-dropdown .dropdown-item {
            color: var(--text-secondary);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        .user-dropdown .dropdown-item:hover {
            background: var(--primary-50);
            color: var(--primary);
            transform: translateX(5px);
        }

        .user-dropdown .dropdown-item i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 0.9rem;
        }

        /* Main Content */
        .main-content {
            margin-top: 2rem;
            padding-bottom: 2rem;
            min-height: calc(100vh - 200px);
        }

        .content-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Cards */
        .card-custom {
            background: var(--light-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .card-custom:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        /* Footer */
        .footer-custom {
            background: var(--light);
            border-top: 1px solid var(--border);
            padding: 2rem 0;
            margin-top: 3rem;
        }

        .footer-custom p {
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
        }

        .footer-custom small {
            color: var(--text-muted);
        }

        /* Animation */
        .animate-card {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.6s ease forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.3rem;
            }

            .nav-link {
                padding: 0.75rem 1rem;
                margin: 0.1rem;
                border-radius: 10px;
            }

            .nav-link.active::before {
                display: none;
            }

            .main-content {
                margin-top: 1rem;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand" href="{{ route('petugas.dashboard') }}">
                <i class="fas fa-user-cog"></i>
                <span>SARPRAS PETUGAS</span>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}"
                           href="{{ route('petugas.dashboard') }}">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('petugas.pengaduan.index') ? 'active' : '' }}"
                           href="{{ route('petugas.pengaduan.index') }}">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Pengaduan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('petugas.riwayat') ? 'active' : '' }}"
                           href="{{ route('petugas.riwayat') }}">
                            <i class="fas fa-history"></i>
                            <span>Riwayat</span>
                        </a>
                    </li>
                </ul>

                <!-- User Menu -->
                <ul class="navbar-nav align-items-center gap-2">
                    <li class="nav-item">
                        <button id="themeToggle" class="btn theme-toggle" aria-label="Toggle theme">
                            <i class="fas fa-moon"></i>
                        </button>
                    </li>
                    <li class="nav-item dropdown user-dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>
                            <span>{{ Auth::user()->nama_pengguna ?? 'Petugas' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger w-100 text-start">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container content-container">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container text-center">
            <p class="mb-2">
                &copy; {{ date('Y') }} <strong class="text-primary">Aplikasi Sarpras</strong> - Petugas Panel
            </p>
            <small>Sistem Pengaduan Sarana dan Prasarana</small>
        </div>
    </footer>

    <!-- Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Active nav link highlight
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');

            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });

            // Animation for cards
            const cards = document.querySelectorAll('.animate-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Theme toggle with localStorage
        (function() {
            const root = document.documentElement;
            const toggleBtn = document.getElementById('themeToggle');
            const saved = localStorage.getItem('theme');
            if (saved === 'dark') {
                root.classList.add('dark');
                toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
            }
            toggleBtn?.addEventListener('click', function() {
                root.classList.toggle('dark');
                const isDark = root.classList.contains('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                toggleBtn.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
