<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;

    protected $table = 'pengaduan';

    protected $primaryKey = 'id_pengaduan';

    protected $fillable = [
        'nama_pengaduan',
        'deskripsi',
        'lokasi',
        'foto',
        'foto_penyelesaian',
        'status',
        'id_user',
        'id_petugas',
        'id_item',
        'item_baru',
        'tgl_pengajuan',
        'tgl_selesai',
        'saran_petugas',
    ];

    // tampilkan accessor di JSON otomatis
    protected $appends = ['foto_url', 'foto_penyelesaian_url'];

    // Tambahkan konstanta status
    const STATUS_DIAJUKAN = 'Diajukan';
    const STATUS_DISETUJUI = 'Disetujui';
    const STATUS_DITOLAK = 'Ditolak';
    const STATUS_DIPROSES = 'Diproses';
    const STATUS_SELESAI = 'Selesai';

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'id_petugas');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item');
    }


    // Perbaiki accessor untuk foto URL
    public function getFotoUrlAttribute()
    {
        if (!$this->foto) {
            return null;
        }

        // Cek jika foto sudah berupa URL lengkap
        if (filter_var($this->foto, FILTER_VALIDATE_URL)) {
            return $this->foto;
        }

        // Cek jika file exists di storage
        if (file_exists(storage_path('app/public/' . $this->foto))) {
            return asset('storage/' . $this->foto);
        }

        // Fallback ke path langsung (kembali path, Flutter akan konstruksi URL)
        return $this->foto;
    }

    public function getFotoPenyelesaianUrlAttribute()
    {
        if (!$this->foto_penyelesaian) {
            return null;
        }

        if (filter_var($this->foto_penyelesaian, FILTER_VALIDATE_URL)) {
            return $this->foto_penyelesaian;
        }

        if (file_exists(storage_path('app/public/' . $this->foto_penyelesaian))) {
            return asset('storage/' . $this->foto_penyelesaian);
        }

        return $this->foto_penyelesaian;
    }

    // Method untuk mengecek apakah bisa diupdate oleh admin
    public function canBeUpdatedByAdmin()
    {
        return in_array($this->status, [self::STATUS_DIAJUKAN]);
    }

    // Method untuk mengecek apakah bisa diupdate oleh petugas
    public function canBeUpdatedByPetugas()
    {
        return in_array($this->status, [self::STATUS_DISETUJUI, self::STATUS_DIPROSES]);
    }
}
