<?php

namespace App\Models\Transaction;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Magang extends AppModel
{
    use SoftDeletes;

    protected $table = 't_magang';
    protected $primaryKey = 'magang_id';

    protected static $_table = 't_magang';
    protected static $_primaryKey = 'magang_id';

    protected $fillable = [
        'mahasiswa_id',
        'mitra_id',
        'periode_id',
        'prodi_id',
        'magang_kode',
        'magang_skema',
        'magang_tipe',
        'is_accept',
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
        //'App\Models\Master\EmployeeModel' => 'jabatan_id'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo('App\Models\Master\MahasiswaModel', 'mahasiswa_id', 'mahasiswa_id');
    }

    public function mitra()
    {
        return $this->belongsTo('App\Models\MitraModel', 'mitra_id', 'mitra_id');
    }

    public function periode()
    {
        return $this->belongsTo('App\Models\Master\PeriodeModel', 'periode_id', 'periode_id');
    }

    public function prodi()
    {
        return $this->belongsTo('App\Models\Master\ProdiModel', 'prodi_id', 'prodi_id');
    }
}
