<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('temporary_item', function (Blueprint $table) {
            $table->id('id_temporary');
            $table->unsignedBigInteger('id_item')->nullable();
            $table->string('nama_barang_baru', 50);
            $table->string('lokasi_barang_baru', 50);

            $table->foreign('id_item')->references('id_item')->on('items')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('temporary_item');
    }
};
