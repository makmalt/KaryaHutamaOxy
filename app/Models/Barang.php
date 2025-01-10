<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;
    protected $table = 'barangs';
    protected $fillable = ['nama_barang', 'deskripsi', 'image', 'barcode', 'harga', 'stok_tersedia', 'supplier_id', 'sku_id', 'hargaBeli'];
    protected static function boot()
    {
        parent::boot();
        static::updating(
            function ($model) {
                if ($model->isDirty('image') && ($model->getOriginal('image') !== null)) {
                    Storage::disk('public')->delete($model->getOriginal('image'));
                }
            }
        );
    }


    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function barangTransaksi()
    {
        return $this->hasMany(BarangTransaksi::class, 'barang_id');
    }
}
