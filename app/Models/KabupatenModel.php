<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KabupatenModel extends Model
{
    use HasFactory;

    protected $table = 'd_kabkota';

    protected $fillable = [
        'd_provinsi_id',
        'nama_kab_kota',
    ];

    public function provinsi()
    {
        return $this->belongsTo(ProvinsiModel::class, 'd_provinsi_id', 'id');
    }
}
