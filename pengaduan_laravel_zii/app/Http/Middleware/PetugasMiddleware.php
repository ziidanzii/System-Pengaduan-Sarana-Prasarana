<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PetugasMiddleware
{
   // app/Http/Middleware/PetugasMiddleware.php
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $role = strtolower(auth()->user()->role);
            if ($role === 'petugas') {
                return $next($request);
            }
        }

        abort(403, 'Akses ditolak! Khusus Petugas.');
    }

}
