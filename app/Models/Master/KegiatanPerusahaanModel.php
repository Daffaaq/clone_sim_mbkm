<?php

namespace App\Models\Master;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KegiatanPerusahaanModel extends AppModel
{
    use SoftDeletes;

    // protected $casts = [
    //     'prodi_id' => 'array',
    // ];

    protected $table = 'm_kegiatan_perusahaan';
    protected $primaryKey = 'kegiatan_perusahaan_id';

    protected static $_table = 'm_kegiatan_perusahaan';
    protected static $_primaryKey = 'kegiatan_perusahaan_id';

    protected $fillable = [
        'perusahaan_id',
        'kode_kegiatan',
        'tipe_kegiatan_id',
        'jenis_magang_id',
        'periode_id',
        'prodi_id',
        'posisi_lowongan',
        'deskripsi',
        'kuota',
        'mulai_kegiatan',
        'akhir_kegiatan',
        'batas_pendaftaran',
        'contact_person',
        'kualifikasi',
        'fasilitas',
        'flyer',
        'status',
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

    public function perusahaan()
    {
        return $this->belongsTo(PerusahaanModel::class, 'perusahaan_id', 'perusahaan_id');
    }

    public function tipe_kegiatan()
    {
        return $this->belongsTo(TipeKegiatanModel::class, 'tipe_kegiatan_id', 'tipe_kegiatan_id');
    }

    public function jenis_magang()
    {
        return $this->belongsTo(JenisMagangModel::class, 'jenis_magang_id', 'jenis_magang_id');
    }
}
