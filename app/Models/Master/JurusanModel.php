<?php

namespace App\Models\Master;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class JurusanModel extends AppModel
{
    use SoftDeletes;

    protected $table = 'm_jurusan';
    protected $primaryKey = 'jurusan_id';
    protected $uniqueKey = 'jurusan_code';

    protected static $_table = 'm_jurusan';
    protected static $_primaryKey = 'jurusan_id';
    protected static $_uniqueKey = 'jurusan_code';

    protected $fillable = [
        'jurusan_code',
        'jurusan_name',
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
}
