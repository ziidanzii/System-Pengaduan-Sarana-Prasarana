<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'nama_item',
        'lokasi',
        'deskripsi',
        'foto',
    ];

    // Relasi many-to-many dengan lokasi melalui list_lokasi
    public function lokasis()
    {
        return $this->belongsToMany(\App\Models\Lokasi::class, 'list_lokasi', 'id_item', 'id_lokasi');
    }

    // Scope untuk mendapatkan item berdasarkan lokasi
    public function scopeByLokasi($query, $namaLokasi)
    {
        return $query->whereHas('lokasis', function($q) use ($namaLokasi) {
            $q->where('nama_lokasi', $namaLokasi);
        });
    }
}
