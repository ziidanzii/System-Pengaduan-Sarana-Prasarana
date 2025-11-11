<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lokasi;

class LokasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lokasi = Lokasi::orderBy('nama_lokasi', 'asc')->get();

        // Return id_lokasi dan nama_lokasi untuk dropdown
        $formattedLokasi = $lokasi->map(function ($lok) {
            return [
                'id_lokasi' => $lok->id_lokasi,
                'nama_lokasi' => $lok->nama_lokasi,
            ];
        });

        return response()->json($formattedLokasi);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $lokasi = Lokasi::with('items')->findOrFail($id);
        return response()->json($lokasi);
    }
}

