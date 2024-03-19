<?php

namespace App\Models;

use App\Models\Master\MahasiswaModel;
use App\Models\Transaction\Magang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DokumenMagangModel extends Model
{
    use SoftDeletes;

    protected $table = 't_dokumen_magang';
    protected $primaryKey = 'dokumen_magang_id';

    protected static $_table = 't_dokumen_magang';
    protected static $_primaryKey = 'dokumen_magang_id';

    protected $fillable = [
        'mahasiswa_id',
        'magang_id',
        'dokumen_magang_nama',
        'dokumen_magang_tipe',
        'dokumen_magang_file',
        'dokumen_magang_status',
        'dokumen_magang_leterangan',
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
        return $this->belongsTo(MahasiswaModel::class, 'mahasiswa_id', 'mahasiswa_id');
    }

    public function magang()
    {
        return $this->belongsTo(Magang::class, 'magang_id', 'magang_id');
    }
}
