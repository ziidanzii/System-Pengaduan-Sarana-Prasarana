<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens; // ⬅️ WAJIB ADA
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Petugas;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable; // ⬅️ TAMBAHKAN HasApiTokens DI SINI

    protected $fillable = [
        'nama_pengguna',
        'username',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function petugas()
    {
        return $this->hasOne(\App\Models\Petugas::class, 'id_user', 'id');
    }
}
