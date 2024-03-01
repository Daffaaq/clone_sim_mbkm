<?php

namespace App\Models\Master;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ProdiModel extends AppModel
{
    use SoftDeletes;

    protected $table = 'm_prodi';
    protected $primaryKey = 'prodi_id';
    protected $uniqueKey = 'prodi_code';

    protected static $_table = 'm_prodi';
    protected static $_primaryKey = 'prodi_id';
    protected static $_uniqueKey = 'prodi_code';

    protected $fillable = [
        'jurusan_id',
        'prodi_code',
        'prodi_name',
        'is_active',
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
        //'App\Models\Master\EmployeeModel' => 'jabatan_id'
    ];

    public function jurusan()
    {
        return $this->belongsTo(JurusanModel::class, 'jurusan_id');
    }
}
