<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id('id_pengaduan');
            $table->string('nama_pengaduan', 200);
            $table->text('deskripsi')->nullable();
            $table->string('lokasi', 200);
            $table->string('foto', 200)->nullable();
            $table->enum('status', ['Diajukan', 'Disetujui', 'Ditolak', 'Diproses', 'Selesai'])->default('Diajukan');

            // Relasi ke users (default Laravel PK = id)
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Relasi ke petugas (PK = id_petugas)
            $table->unsignedBigInteger('id_petugas')->nullable();
            $table->foreign('id_petugas')
                  ->references('id_petugas')
                  ->on('petugas')
                  ->onDelete('set null');

            // Relasi ke items (PK = id_item)
            $table->unsignedBigInteger('id_item')->nullable();
            $table->foreign('id_item')
                  ->references('id_item')
                  ->on('items')
                  ->onDelete('set null');

            $table->date('tgl_pengajuan');
            $table->date('tgl_selesai')->nullable();
            $table->text('saran_petugas')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pengaduan');
    }
};
