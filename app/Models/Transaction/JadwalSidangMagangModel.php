<?php

namespace App\Models\Transaction;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JadwalSidangMagangModel extends AppModel
{
    use SoftDeletes;

    protected $table = 't_jadwal_sidang_magang';
    protected $primaryKey = 'jadwal_sidang_magang_id';

    protected static $_table = 't_jadwal_sidang_magang';
    protected static $_primaryKey = 'jadwal_sidang_magang_id';

    protected $fillable = [
        'semhas_daftar_id',
        'tanggal_sidang',
        'jam_sidang_mulai',
        'jam_sidang_selesai',
        'jenis_sidang',
        'tempat',
        'gedung',
        'periode_id',
        'periode_id',
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
        //'App\Models\Master\EmployeeModel' => 'jabatan_id'
    ];


    public function daftarSemhas()
    {
        return $this->belongsTo('App\Models\Transaction\SemhasDaftarModel', 'semhas_daftar_id', 'semhas_daftar_id');
    }
    public function periode()
    {
        return $this->belongsTo('App\Models\Master\PeriodeModel', 'periode_id', 'periode_id');
    }
}
