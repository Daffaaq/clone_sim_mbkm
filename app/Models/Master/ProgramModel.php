<?php

namespace App\Models\Master;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramModel extends AppModel
{
    use SoftDeletes;

    protected $table = 'm_program';
    protected $primaryKey = 'program_id';

    protected static $_table = 'm_program';
    protected static $_primaryKey = 'program_id';

    protected $fillable = [
        'program_kode',
        'program_nama',
        'program_deskripsi',
        'program_bulan',

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

    //relation to periode and prodi
    public function periode()
    {
        return $this->belongsTo(PeriodeModel::class, 'periode_id');
    }

    public function prodi()
    {
        return $this->belongsTo(ProdiModel::class, 'prodi_id');
    }
}
