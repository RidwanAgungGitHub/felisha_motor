<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReorderPoint extends Model
{
    use HasFactory;

    protected $table = 'reorder_points';

    protected $fillable = [
        'barang_id',
        'safety_stock',
        'lead_time',
        'permintaan_per_periode',
        'total_hari_kerja',
        'hasil'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
