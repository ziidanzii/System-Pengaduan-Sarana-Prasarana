<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryItem extends Model
{
    use HasFactory;

    protected $table = 'temporary_item';
    protected $primaryKey = 'id_temporary';

    protected $fillable = [
        'id_item',
        'nama_barang_baru',
        'lokasi_barang_baru',
        'status',
        'id_user',
        'id_pengaduan',
        'alasan_penolakan' // TAMBAHKAN INI
    ];

    // Jika tabel tidak memiliki timestamps, tambahkan:
    // public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'id_pengaduan');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item');
    }
}
