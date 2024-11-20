<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarangTransaksi extends Model
{
    //
    use HasFactory;
    protected $table = 'barang_transaksis';
    protected $fillable = ['barang_transaksi_id', 'transaksi_id', 'barang_id', 'harga_barang', 'quantity', 'total_harga',];
    public function transaksi(){
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
    public function barang(){
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
