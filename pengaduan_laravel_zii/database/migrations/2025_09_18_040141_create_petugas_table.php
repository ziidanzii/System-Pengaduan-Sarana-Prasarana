<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('petugas', function (Blueprint $table) {
            $table->id('id_petugas');
            $table->string('nama', 200);
            $table->enum('gender', ['P','L']);
            $table->string('telp', 30);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('petugas');
    }
};
