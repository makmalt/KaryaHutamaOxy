<?php 
namespace app\Services;
use App\Models\Barang;

class BarangTransaksiServices {
    public function reduceStock(int $barangId, int $quantity): void
    {
        $barang = Barang::findOrFail($barangId);

        if ($barang->stok_tersedia < $quantity) {
            throw new \Exception('Stok barang tidak mencukupi!');
        }

        $barang->decrement('stok_tersedia', $quantity);
    }
}