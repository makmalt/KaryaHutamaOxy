<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    //
    use HasFactory;
    protected $table = 'transaksis';
    protected $fillable = ['no_transaksi', 'tgl_transaksi', 'grand_total'];

    public function barangTransaksi()
    {
        return $this->hasMany(BarangTransaksi::class, 'transaksi_id');
    }

    protected $casts = [
        'tgl_transaksi' => 'datetime',
    ];
}
