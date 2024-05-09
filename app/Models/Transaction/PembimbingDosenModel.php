<?php

namespace App\Models\Transaction;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembimbingDosenModel extends AppModel
{
    use SoftDeletes;

    protected $table = 't_pembimbing_dosen';
    protected $primaryKey = 'pembimbing_dosen_id';

    protected static $_table = 't_pembimbing_dosen';
    protected static $_primaryKey = 'pembimbing_dosen_id';

    protected $fillable = [
        'mahasiswa_id',
        'magang_id',
        'dosen_id',
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

    public function mahasiswa()
    {
        return $this->belongsTo('App\Models\Master\MahasiswaModel', 'mahasiswa_id', 'mahasiswa_id');
    }

    public function magang()
    {
        return $this->belongsTo('App\Models\Transaction\Magang', 'magang_id', 'magang_id');
    }

    public function dosen()
    {
        return $this->belongsTo('App\Models\Master\DosenModel', 'dosen_id', 'dosen_id');
    }

    public function periode()
    {
        return $this->belongsTo('App\Models\Master\PeriodeModel', 'periode_id', 'periode_id');
    }
}
