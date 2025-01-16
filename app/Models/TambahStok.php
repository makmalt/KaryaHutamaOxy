<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use app\Services\TambahStokServices;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TambahStok extends Model
{
    //
    use HasFactory;
    protected $table = 'tambah_stok';
    protected $fillable = [
        'barang_id',
        'supplier_id',
        'tgl_masuk',
        'kuantitas',
        'harga_satuan',
        'total_harga',
        'keterangan',
    ];

    protected static function booted()
    {
        static::deleting(function ($tambahStok) {
            app(TambahStokServices::class)->kurangiStok($tambahStok);
        });

        static::created(function ($tambahStok) {
            DB::transaction(function () use ($tambahStok) {
                app(TambahStokServices::class)->tambahStok($tambahStok);
            });
        });
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
