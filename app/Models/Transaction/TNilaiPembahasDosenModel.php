<?php

namespace App\Models\Transaction;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TNilaiPembahasDosenModel extends AppModel
{
    use SoftDeletes;

    protected $table = 't_nilai_pembahas_dosen';
    protected $primaryKey = 't_nilai_pembahas_dosen_id';

    protected static $_table = 't_nilai_pembahas_dosen';
    protected static $_primaryKey = 't_nilai_pembahas_dosen_id';

    protected $fillable = [
        'nilai',
        'nilai_pembahas_dosen_id',
        'semhas_daftar_id',
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
    public function kriteriaNilai()
    {
        return $this->belongsTo('App\Models\Master\NilaiPembahasDosenModel', 'nilai_pembahas_dosen_id', 'nilai_pembahas_dosen_id');
    }
}
