<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $role = strtolower(Auth::user()->role);

        if (!in_array($role, ['admin', 'administrator'])) {
            // Jika user mencoba akses admin route
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak. Khusus administrator.'], 403);
            }

            // Kalau lewat browser, redirect saja
            switch ($role) {
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
