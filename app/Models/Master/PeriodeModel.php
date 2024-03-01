<?php

namespace App\Models\Master;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeriodeModel extends AppModel
{
    use SoftDeletes;

    protected $table = 'm_periode';
    protected $primaryKey = 'periode_id';

    protected static $_table = 'm_periode';
    protected static $_primaryKey = 'periode_id';

    protected $fillable = [
        'periode_nama',
        'periode_direktur',
        'periode_nip',
        'is_active',
        'is_current',
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
