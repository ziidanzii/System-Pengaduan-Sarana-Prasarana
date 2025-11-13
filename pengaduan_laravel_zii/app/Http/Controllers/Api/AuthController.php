<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login API
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Bisa login pakai email atau username
        $user = User::where('email', $request->email)
            ->orWhere('username', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Email/Username atau password salah.',
            ], 401);
        }

        // Buat token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil.',
            'user' => [
                'id' => $user->id,
                'nama_pengguna' => $user->nama_pengguna,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $user->role,
            ],
            'token' => $token,
        ]);
    }
    /**
 * Register API
 */
public function register(Request $request)
{
    $request->validate([
        'nama_pengguna' => 'required|string|max:255',
        'username' => 'required|string|unique:users,username|max:255',
        'email' => 'required|email|unique:users,email|max:255',
        'password' => 'required|string|min:6|confirmed',
    ]);

    // Buat user baru dengan role default 'user'
    $user = User::create([
        'nama_pengguna' => $request->nama_pengguna,
        'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'user', // Default role
    ]);
}
/**
 * Update profile API
 */
public function updateProfile(Request $request)
{
    $user = $request->user();

    $request->validate([
        'nama_pengguna' => 'required|string|max:255',
        'username' => 'required|string|unique:users,username,' . $user->id,
        'password' => 'nullable|string|min:6',
    ]);

    $user->nama_pengguna = $request->nama_pengguna;
    $user->username = $request->username;

    if ($request->has('password') && $request->password) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return response()->json([
        'status' => true,
        'message' => 'Profil berhasil diperbarui.',
        'user' => [
            'id' => $user->id,
            'nama_pengguna' => $user->nama_pengguna,
            'email' => $user->email,
            'username' => $user->username,
            'role' => $user->role,
        ],
    ]);
// Buat token Sanctum
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'status' => true,
        'message' => 'Registrasi berhasil.',
        'user' => [
            'id' => $user->id,
            'nama_pengguna' => $user->nama_pengguna,
            'email' => $user->email,
            'username' => $user->username,
            'role' => $user->role,
        ],
        'token' => $token,
    ], 201);
}

    /**
     * Logout API
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Ambil data user login
     */
    public function me(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => $request->user(),
        ]);
    }
}
