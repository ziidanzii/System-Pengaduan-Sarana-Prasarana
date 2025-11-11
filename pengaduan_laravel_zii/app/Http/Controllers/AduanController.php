<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengaduan;
use App\Models\TemporaryItem;
use App\Models\Item;
use App\Models\Lokasi;

class AduanController extends Controller
{
    public function create()
    {
        $lokasiList = Lokasi::pluck('nama_lokasi')->toArray();
        $items = Item::all();

        return view('pengguna.pengaduan.create_aduan', compact('lokasiList', 'items'));
    }

    public function getItemsByLokasi(Request $request)
    {
        $namaLokasi = $request->lokasi;

        if (!$namaLokasi) {
            return response()->json([]);
        }

        $items = Item::with('lokasis')
            ->whereHas('lokasis', function($query) use ($namaLokasi) {
                $query->where('nama_lokasi', $namaLokasi);
            })
            ->get()
            ->map(function($item) {
                return [
                    'id_item'   => $item->id_item,
                    'nama_item' => $item->nama_item,
                    'lokasi'    => $item->lokasis->pluck('nama_lokasi')->implode(', ')
                ];
            });

        return response()->json($items);
    }

    public function store(Request $request)
    {
        // 1. VALIDASI
        $request->validate([
            'nama_pengaduan' => 'required|string|max:200',
            'deskripsi'      => 'required|string',
            'lokasi'         => 'required|string|max:200',
            'id_item'        => 'nullable|exists:items,id_item',
            'item_baru'      => 'nullable|string|max:500',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'id_item.required_without' => 'Pilih item yang ada atau request item baru',
            'item_baru.required_without' => 'Pilih item yang ada atau request item baru',
        ]);

        // 2. Validasi custom untuk memastikan salah satu dipilih
        if ($request->filled('item_baru')) {
        $itemCount = count(array_filter(explode(',', $request->item_baru), 'trim'));
        if ($itemCount > 1) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['item_baru' => 'Hanya boleh request 1 item baru per pengaduan.']);
        }
    }

        // 3. SIAPKAN DATA PENGADUAN UTAMA
        $data = [
            'nama_pengaduan' => $request->nama_pengaduan,
            'deskripsi'      => $request->deskripsi,
            'lokasi'         => $request->lokasi,
            'id_user'        => auth()->id(),
            'tgl_pengajuan'  => now(),
            'status'         => 'Diajukan'
        ];

        // Jika memilih item existing, gunakan id_item
        if ($request->id_item) {
            $data['id_item'] = $request->id_item;
        }

        // Handle upload foto
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('pengaduan', 'public');
            $data['foto'] = $path;
        }

        // 4. Buat pengaduan utama
        $pengaduan = Pengaduan::create($data);

        // 5. Handle request item baru
        if ($request->filled('item_baru')) {
            $newItems = collect(explode(',', $request->item_baru))
                            ->map(fn($item) => trim($item))
                            ->filter()
                            ->unique();

            foreach ($newItems as $namaItem) {
                $namaItem = substr($namaItem, 0, 200);

                TemporaryItem::create([
                    'nama_barang_baru'   => $namaItem,
                    'lokasi_barang_baru' => $request->lokasi,
                    'status'             => 'pending',
                    'id_user'            => auth()->id(),
                    'id_pengaduan'       => $pengaduan->id_pengaduan
                ]);
            }
        }

        return redirect()->route('user.aduan.history')
            ->with('success', 'Aduan berhasil diajukan!' .
            ($request->filled('item_baru') ? ' Request item baru juga telah dikirim untuk persetujuan admin.' : ''));
    }

    /**
     * Riwayat aduan
     */
    public function history(Request $request)
    {
        $user = Auth::user();

        $query = Pengaduan::where('id_user', $user->id)
                          ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $aduanList = $query->get();

        return view('pengguna.pengaduan.history', compact('aduanList'));
    }
}
