<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use app\Services\BarangTransaksiServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarangTransaksi extends Model
{
    //
    use HasFactory;
    protected $table = 'barang_transaksis';
    protected $fillable = ['transaksi_id', 'barang_id', 'harga_barang', 'quantity', 'total_harga',];
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    protected static function booted()
    {
        static::created(function ($barangTransaksi) {
            app(BarangTransaksiServices::class)->reduceStock(
                $barangTransaksi->barang_id,
                $barangTransaksi->quantity
            );
        });
    }
}
