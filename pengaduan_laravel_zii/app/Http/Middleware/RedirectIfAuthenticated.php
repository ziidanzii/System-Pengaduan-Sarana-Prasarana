<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
   // File: app/Http/Middleware/RedirectIfAuthenticated.php

// ... (kode lainnya)

public function handle($request, Closure $next, ...$guards)
{
    if (Auth::check()) {
        $currentRoute = $request->route() ? $request->route()->getName() : null;
        $guestRoutes = ['login', 'login.post', 'register', 'register.post'];

        if (in_array($currentRoute, $guestRoutes)) {
            $role = strtolower(Auth::user()->role);
            if ($role === 'administrator' || $role === 'admin') { // Tambahkan 'admin' untuk fleksibilitas
                return redirect()->route('admin.dashboard'); // Menggunakan 'admin.dashboard'
            } elseif ($role === 'petugas') {
                return redirect()->route('petugas.dashboard');
            } else {
                return redirect()->route('user.dashboard'); // Menggunakan 'user.dashboard'
            }
        }
    }

    return $next($request);
}

// ... (kode lainnya)
}
