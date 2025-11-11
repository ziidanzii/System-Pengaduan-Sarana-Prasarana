<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('petugas', function (Blueprint $table) {
        $table->unsignedBigInteger('id_user')->after('telp')->nullable();
        $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('petugas', function (Blueprint $table) {
        $table->dropForeign(['id_user']);
        $table->dropColumn('id_user');
    });
}

};
