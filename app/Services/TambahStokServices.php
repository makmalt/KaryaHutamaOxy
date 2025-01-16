<?php

namespace app\Services;

use App\Models\TambahStok;

class TambahStokServices
{
    public function kurangiStok(TambahStok $tambahStok): void
    {
        $barang = $tambahStok->barang;

        // Mengurangi stok_tersedia barang berdasarkan kuantitas yang ada di TambahStok
        if ($barang) {
            $barang->stok_tersedia -= $tambahStok->kuantitas;
            $barang->save();
        }
    }

    public function tambahStok(TambahStok $tambahStok): void
    {
        $barang = $tambahStok->barang;

        // Mengurangi stok_tersedia barang berdasarkan kuantitas yang ada di TambahStok
        if ($barang) {
            $barang->stok_tersedia += $tambahStok->kuantitas;
            $barang->save();
        }
    }
}
