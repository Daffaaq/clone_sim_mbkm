<?php

namespace App\Models\Master;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KegiatanModel extends AppModel
{
    use SoftDeletes;

    protected $table = 'm_kegiatan';
    protected $primaryKey = 'kegiatan_id';

    protected static $_table = 'm_kegiatan';
    protected static $_primaryKey = 'kegiatan_id';

    protected $fillable = [
        'program_id',
        'kegiatan_kode',
        'kegiatan_nama',
        'kegiatan_skema',
        'kegiatan_deskripsi',
        'is_kuota',
        'is_mandiri',
        'is_submit_proposal'
    ];
    protected static $cascadeDelete = false;   //  True: Force Delete from Parent (cascade)
    protected static $childModel = [
        //  Model => columnFK
        //'App\Models\Master\EmployeeModel' => 'jabatan_id'
    ];

    //relation to periode and prodi
    public function program()
    {
        return $this->belongsTo(ProgramModel::class, 'program_id');
    }
}
