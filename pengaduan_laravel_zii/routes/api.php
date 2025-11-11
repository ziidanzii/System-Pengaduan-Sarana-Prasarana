<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PengaduanApiController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\LokasiController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\AdminPengaduanController;

Route::post('/login', [AuthController::class, 'login']);

// Public routes untuk lokasi dan item (tidak perlu auth)
Route::get('/lokasi', [LokasiController::class, 'index']);
Route::get('/item', [ItemController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/pengaduan', [PengaduanApiController::class, 'index']);
    Route::post('/pengaduan', [PengaduanApiController::class, 'store']);
    Route::get('/pengaduan/{id}', [PengaduanApiController::class, 'show']);
    Route::delete('/pengaduan/{id}', [PengaduanApiController::class, 'destroy']);
    
    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::get('/pengaduan', [AdminPengaduanController::class, 'index']);
        Route::get('/pengaduan/{id}', [AdminPengaduanController::class, 'show']);
        Route::put('/pengaduan/{id}', [AdminPengaduanController::class, 'update']);
    });
});
