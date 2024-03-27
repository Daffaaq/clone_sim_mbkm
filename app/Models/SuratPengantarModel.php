<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratPengantarModel extends AppModel
{
    use SoftDeletes;

    protected $table = 't_surat_pengantar';
    protected $primaryKey = 'surat_pengantar_id';

    protected static $_table = 't_surat_pengantar';
    protected static $_primaryKey = 'surat_pengantar_id';

    protected $fillable = [
        'surat_pengantar_no',
        'magang_kode',
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
