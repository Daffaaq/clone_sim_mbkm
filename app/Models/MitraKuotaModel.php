<?php

namespace App\Models;

use App\Models\Master\ProdiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MitraKuotaModel extends AppModel
{
    use SoftDeletes;

    protected $table = 'd_mitra_kuota';
    protected $primaryKey = 'mitra_kuota_id';

    protected static $_table = 'd_mitra_kuota';
    protected static $_primaryKey = 'mitra_kuota_id';

    protected $fillable = [
        'mitra_id',
        'prodi_id',
        'kuota',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected static $cascadeDelete = false;   //  True: Force Delete from Parent (cascade)
    protected static $childModel = [
        //  Model => columnFK
        // 'App\Models\Master\DosenModel' => 'jurusan_id'
    ];

    //relation to periode and prodi
    public function mitra()
    {
        return $this->belongsTo(MitraModel::class, 'mitra_id');
    }

    public function prodi()
    {
        return $this->belongsTo(ProdiModel::class, 'prodi_id');
    }
}
