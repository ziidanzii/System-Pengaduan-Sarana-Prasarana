<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';
    protected $primaryKey = 'id_lokasi';

    protected $fillable = [
        'nama_lokasi'
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'list_lokasi', 'id_lokasi', 'id_item');
    }

    public function listLokasi()
    {
        return $this->hasMany(ListLokasi::class, 'id_lokasi', 'id_lokasi');
    }
}
