<?php

namespace App\Http\Controllers;

use App\Http\Resources\BarangCollection;
use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    //
    public function index()
    {
        $barang = Barang::paginate(5); // Atau `all()` jika tanpa pagination
        return new BarangCollection($barang);
    }
}
