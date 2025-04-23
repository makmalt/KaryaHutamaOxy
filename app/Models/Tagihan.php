<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    //
    use HasFactory;
    protected $table = 'tagihans';
    protected $fillable = [
        'tagihan',
        'nominal_tagihan',
        'jatuhTempo_tagihan',
        'supplier_id',
        'status_lunas',
        'keterangan',
        'img_nota'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
