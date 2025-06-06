<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafetyStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'pemakaian_maksimum',
        'pemakaian_rata_rata',
        'lead_time',
        'hasil',
        'bulan'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
