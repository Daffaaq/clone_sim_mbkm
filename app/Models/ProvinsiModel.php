<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvinsiModel extends Model
{
    use HasFactory;

    protected $table = 'd_provinsi';

    protected $fillable = [
        'nama_provinsi',
    ];

    public function kabupaten()
    {
        return $this->hasMany(KabupatenModel::class, 'id_provinsi', 'id');
    }
}
