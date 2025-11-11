<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Lokasi;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::with('lokasis');

        // Filter by id_lokasi jika ada parameter id_lokasi
        if ($request->has('id_lokasi') && $request->id_lokasi) {
            $query->whereHas('lokasis', function($q) use ($request) {
                $q->where('lokasi.id_lokasi', $request->id_lokasi);
            });
        }

        $items = $query->orderBy('nama_item', 'asc')->get();

        // Format response untuk Flutter
        $formattedItems = $items->map(function ($item) {
            return [
                'id_item' => $item->id_item,
                'nama_item' => $item->nama_item,
                'lokasi' => $item->lokasi,
                'deskripsi' => $item->deskripsi,
                'foto' => $item->foto,
            ];
        });

        return response()->json($formattedItems);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = Item::with('lokasis')->findOrFail($id);
        return response()->json($item);
    }
}

