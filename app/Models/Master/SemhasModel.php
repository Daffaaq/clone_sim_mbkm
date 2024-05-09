<?php

namespace App\Models\Master;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SemhasModel extends AppModel
{

    use SoftDeletes;
    protected $table = 'm_semhas';
    protected $primaryKey = 'semhas_id';

    protected static $_table = 'm_semhas';
    protected static $_primaryKey = 'semhas_id';

    protected $fillable = [
        'prodi_id',
        'judul_semhas',
        'gelombang',
        'kuota_bimbingan',
        'tanggal_mulai_pendaftaran',
        'tanggal_akhir_pendaftaran',
        'periode_id',
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

    public function prodi()
    {
        return $this->belongsTo(ProdiModel::class, 'prodi_id', 'prodi_id');
    }
    public function periode()
    {
        return $this->belongsTo('App\Models\Master\PeriodeModel', 'periode_id', 'periode_id');
    }
}
