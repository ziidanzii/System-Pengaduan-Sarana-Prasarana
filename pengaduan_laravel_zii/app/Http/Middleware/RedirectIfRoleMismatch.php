<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfRoleMismatch
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = strtolower(Auth::user()->role);

        // Jika role user TIDAK sesuai dengan yang diizinkan
        if (!in_array($userRole, $roles)) {
            // 🔁 Redirect otomatis ke dashboard sesuai role-nya
            switch ($userRole) {
                case 'admin':
                case 'administrator':
                    return redirect()->route('admin.dashboard');
                case 'petugas':
                    return redirect()->route('petugas.dashboard');
                case 'pengguna':
                case 'user':
                    return redirect()->route('user.dashboard');
                default:
                    abort(403, 'Akses ditolak.');
            }
        }

        return $next($request);
    }
}
