<?php

namespace App\Models\Master;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstrukturModel extends AppModel
{

    use SoftDeletes;
    protected $table = 'm_instruktur';
    protected $primaryKey = 'instruktur_id';

    protected static $_table = 'm_instruktur';
    protected static $_primaryKey = 'instruktur_id';

    protected $fillable = [
        'nama_instruktur',
        'instruktur_email',
        'instruktur_phone',
        'password',
        'user_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected static $cascadeDelete = false;   //  True: Force Delete from Parent (cascade)
    protected static $childModel = [
        //  Model => columnFK
        // 'App\Models\Master\DosenModel' => 'jurusan_id'
    ];
}
