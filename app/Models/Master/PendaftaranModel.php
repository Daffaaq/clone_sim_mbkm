<?php

namespace App\Models\Master;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PendaftaranModel extends AppModel
{
    use SoftDeletes;

    // protected $casts = [
    //     'prodi_id' => 'array',
    // ];

    protected $table = 'm_pendaftaran';
    protected $primaryKey = 'pendaftaran_id';

    protected static $_table = 'm_pendaftaran';
    protected static $_primaryKey = 'pendaftaran_id';

    protected $fillable = [
        'mahasiswa_id',
        'kegiatan_perusahaan_id',
        'periode_id',
        'kode_pendaftaran',
        'tipe_pendaftar',
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

    public function mahasiswa()
    {
        return $this->belongsTo('App\Models\Master\MahasiswaModel', 'mahasiswa_id', 'mahasiswa_id');
    }

    public function kegiatan_perusahaan()
    {
        return $this->belongsTo('App\Models\Master\KegiatanPerusahaanModel', 'kegiatan_perusahaan_id', 'kegiatan_perusahaan_id');
    }

    public function periode()
    {
        return $this->belongsTo('App\Models\Master\PeriodeModel', 'periode_id', 'periode_id');
    }
}
