<?php

namespace App\Models\Transaction;

use App\Models\AppModel;
use App\Models\Master\ProdiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BeritaModel extends AppModel
{
    use SoftDeletes;

    protected $table = 't_berita';
    protected $primaryKey = 'berita_id';

    protected static $_table = 't_berita';
    protected static $_primaryKey = 'berita_id';

    protected $fillable = [
        'berita_uid',
        'prodi_id',
        'berita_judul',
        'berita_isi',
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

    public function prodi()
    {
        return $this->belongsTo(ProdiModel::class, 'prodi_id', 'prodi_id');
    }
}
