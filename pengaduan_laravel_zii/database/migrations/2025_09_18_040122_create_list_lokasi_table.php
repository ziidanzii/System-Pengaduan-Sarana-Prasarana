<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('list_lokasi', function (Blueprint $table) {
            $table->id('id_list');
            $table->unsignedBigInteger('id_lokasi');
            $table->unsignedBigInteger('id_item');

            $table->foreign('id_lokasi')->references('id_lokasi')->on('lokasi')->onDelete('cascade');
            $table->foreign('id_item')->references('id_item')->on('items')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('list_lokasi');
    }
};
