<?php

namespace App\Http\Controllers;

use App\Filament\Resources\BarangResource;
use App\Http\Resources\BarangCollection;
use App\Models\Barang;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;

use function Laravel\Prompts\search;

class BarangController extends Controller
{
    //
    public function index()
    {
        // $barang = Barang::all(); // Atau `all()` jika tanpa pagination
        $barang = Barang::paginate(6); // Atau `all()` jika tanpa pagination
        return new BarangCollection($barang);
    }

    public function search(Request $request)
    {
        $keyword = $request->input('q');
        if (!$keyword) {
            return response()->json(['message' => 'Keyword tidak dikirim'], 400);
        }

        $barang = Barang::where('nama_barang', 'like', '%' . $keyword . '%');

        return response()->json([
            'status' => 'success',
            'data' => $barang->get()
        ]);
    }

    public function show($id)
    {
        $barang = Barang::find($id);

        // Mengganti kategori_id dengan nama kategori
        $barang->kategori_id = $barang->kategori->nama_kategori;

        return response()->json($barang);
    }

    public function findByBarcode($barcode)
    {
        $barang = Barang::where('barcode', $barcode)->first();

        if (!$barang) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        // Mengganti kategori_id dengan nama kategori
        $barang->kategori_id = $barang->kategori->nama_kategori;

        return response()->json($barang);
    }
}
