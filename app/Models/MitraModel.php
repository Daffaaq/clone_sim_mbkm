<?php

namespace App\Models;

use App\Models\AppModel;
use App\Models\Master\KegiatanModel;
use App\Models\Master\PeriodeModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class MitraModel extends AppModel
{
    use SoftDeletes;

    protected $table = 'd_mitra';
    protected $primaryKey = 'mitra_id';

    protected static $_table = 'd_mitra';
    protected static $_primaryKey = 'mitra_id';

    protected $fillable = [
        'kegiatan_id',
        'periode_id',
        'mitra_prodi',
        'mitra_nama',
        'mitra_alamat',
        'mitra_website',
        'mitra_deskripsi',
        'mitra_flyer',
        'mitra_skema',
        'mitra_durasi',
        'provinsi_id',
        'kota_id',
        'status',
        'mitra_keterangan_ditolak',
        'mitra_batas_pendaftaran',
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
    public function kegiatan()
    {
        return $this->belongsTo(KegiatanModel::class, 'kegiatan_id');
    }

    public function periode()
    {
        return $this->belongsTo(PeriodeModel::class, 'periode_id');
    }

    public function provinsi()
    {
        return $this->belongsTo('App\Models\ProvinsiModel', 'provinsi_id', 'id');
    }

    public function kota()
    {
        return $this->belongsTo('App\Models\KabupatenModel', 'kota_id', 'id');
    }
    // public function mitra()
    // {
    //     return $this->belongsTo(MitraModel::class, 'mitra_id');
    // }
}
