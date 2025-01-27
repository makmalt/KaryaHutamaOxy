<?php

namespace App\Http\Controllers;

use App\Filament\Resources\BarangResource;
use App\Http\Resources\BarangCollection;
use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    //
    public function index()
    {
        $barang = Barang::all(); // Atau `all()` jika tanpa pagination
        return new BarangCollection($barang);
    }

    public function show($id)
    {
        $barang = Barang::find($id);

        // Mengganti kategori_id dengan nama kategori
        $barang->kategori = $barang->kategori ? $barang->kategori->nama_kategori : null;

        return response()->json($barang);
    }
}
