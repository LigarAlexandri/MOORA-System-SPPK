<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    use HasFactory;

    // Define the table associated with the model (optional if name is plural of model name)
    protected $table = 'umkms';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'nama_bisnis',
        'omzet_penjualan_juta_idr',
        'profitabilitas_persen',
        'skor_kredit',
        'solvabilitas_der',
        'beban_utang_eksisting_juta_idr_bln',
    ];
}