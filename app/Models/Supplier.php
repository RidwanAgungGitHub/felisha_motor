<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kontak',
        'alamat',
        'kota',
        'provinsi',
        'telp_1',
        'telp_2',
        'email',
    ];

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    public function getNamaKontakAttribute()
    {
        return $this->nama . ' - ' . $this->kontak;
    }
}
