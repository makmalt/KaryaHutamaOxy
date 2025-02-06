<?php

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\TransaksiController;

use function Laravel\Prompts\search;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/barang', [BarangController::class, 'index']);
    Route::get('/barang/search', [BarangController::class, 'search']);
    Route::get('/barang/show/{id}', [BarangController::class, 'show']);
    Route::get('/barang/barcode/{barcode}', [BarangController::class, 'findByBarcode']);
    Route::post('/transaksi', [TransaksiController::class, 'store']);
});
