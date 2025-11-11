<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed users table
        DB::table('users')->insert([
            [
                'nama_pengguna' => 'Admin System',
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pengguna' => 'Petugas Utama',
                'username' => 'petugas1',
                'email' => 'petugas1@gmail.com',
                'password' => Hash::make('petugas123'),
                'role' => 'petugas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pengguna' => 'Pengguna Biasa',
                'username' => 'pengguna1',
                'email' => 'pengguna1@gmail.com',
                'password' => Hash::make('pengguna123'),
                'role' => 'pengguna',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Seed lokasi table
        DB::table('lokasi')->insert([
            [
                'nama_lokasi' => 'Gedung A - Lantai 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lokasi' => 'Gedung A - Lantai 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lokasi' => 'Gedung B - Lantai 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lokasi' => 'Gedung B - Lantai 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lokasi' => 'Laboratorium Komputer',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Seed items table
        DB::table('items')->insert([
            [
                'nama_item' => 'Kursi Kayu',
                'lokasi' => 'Gedung A - Lantai 1',
                'deskripsi' => 'Kursi kayu standar untuk ruang kelas',
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_item' => 'Meja Guru',
                'lokasi' => 'Gedung A - Lantai 1',
                'deskripsi' => 'Meja guru ukuran besar dengan laci',
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_item' => 'Proyektor',
                'lokasi' => 'Gedung A - Lantai 2',
                'deskripsi' => 'Proyektor LCD untuk presentasi',
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_item' => 'Komputer PC',
                'lokasi' => 'Laboratorium Komputer',
                'deskripsi' => 'Komputer PC untuk praktik siswa',
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_item' => 'AC Split',
                'lokasi' => 'Gedung B - Lantai 1',
                'deskripsi' => 'AC split 1 PK untuk ruang kelas',
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Seed petugas table
        DB::table('petugas')->insert([
            [
                'nama' => 'Budi Santoso',
                'gender' => 'L',
                'telp' => '081234567890',
                'id_user' => 2, // Sesuai dengan user petugas1
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Siti Rahayu',
                'gender' => 'P',
                'telp' => '081298765432',
                'id_user' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Seed list_lokasi table (menghubungkan items dengan lokasi)
        DB::table('list_lokasi')->insert([
            [
                'id_lokasi' => 1,
                'id_item' => 1,
            ],
            [
                'id_lokasi' => 1,
                'id_item' => 2,
            ],
            [
                'id_lokasi' => 2,
                'id_item' => 3,
            ],
            [
                'id_lokasi' => 5,
                'id_item' => 4,
            ],
            [
                'id_lokasi' => 3,
                'id_item' => 5,
            ]
        ]);

        // Seed pengaduan table
        DB::table('pengaduan')->insert([
            [
                'nama_pengaduan' => 'Kursi Rusak',
                'deskripsi' => 'Kursi di ruang A101 mengalami kerusakan pada kaki belakang',
                'lokasi' => 'Gedung A - Lantai 1',
                'foto' => null,
                'status' => 'Selesai',
                'id_user' => 3, // Pengguna biasa
                'id_petugas' => 1, // Budi Santoso
                'id_item' => 1, // Kursi Kayu
                'tgl_pengajuan' => '2024-01-15',
                'tgl_selesai' => '2024-01-17',
                'saran_petugas' => 'Kursi telah diperbaiki dengan penambahan baut penguat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pengaduan' => 'Proyektor Tidak Menyala',
                'deskripsi' => 'Proyektor di ruang A201 tidak bisa dinyalakan',
                'lokasi' => 'Gedung A - Lantai 2',
                'foto' => null,
                'status' => 'Diproses',
                'id_user' => 3, // Pengguna biasa
                'id_petugas' => 1, // Budi Santoso
                'id_item' => 3, // Proyektor
                'tgl_pengajuan' => '2024-01-20',
                'tgl_selesai' => null,
                'saran_petugas' => 'Sedang dilakukan pengecekan kabel dan lampu proyektor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pengaduan' => 'AC Bocor',
                'deskripsi' => 'AC di ruang B102 mengeluarkan air dan bocor',
                'lokasi' => 'Gedung B - Lantai 1',
                'foto' => null,
                'status' => 'Diajukan',
                'id_user' => 3, // Pengguna biasa
                'id_petugas' => null,
                'id_item' => 5, // AC Split
                'tgl_pengajuan' => '2024-01-25',
                'tgl_selesai' => null,
                'saran_petugas' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Seed temporary_item table (jika diperlukan)
        DB::table('temporary_item')->insert([
            [
                'id_item' => null,
                'nama_barang_baru' => 'Whiteboard Magnetik',
                'lokasi_barang_baru' => 'Gedung A - Lantai 2',
            ],
            [
                'id_item' => 1,
                'nama_barang_baru' => 'Kursi Plastik',
                'lokasi_barang_baru' => 'Laboratorium Komputer',
            ]
        ]);
    }
}
