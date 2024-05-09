<?php

namespace App\Models\Transaction;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstrukturLapanganModel extends AppModel
{
    use SoftDeletes;

    protected $table = 't_instruktur_lapangan';
    protected $primaryKey = 'instruktur_lapangan_id';

    protected static $_table = 't_instruktur_lapangan';
    protected static $_primaryKey = 'instruktur_lapangan_id';

    protected $fillable = [
        'mahasiswa_id',
        'magang_id',
        'instruktur_id',
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

    public function instruktur()
    {
        return $this->belongsTo('App\Models\Master\InstrukturModel', 'instruktur_id', 'instruktur_id');
    }

    public function periode()
    {
        return $this->belongsTo('App\Models\Master\PeriodeModel', 'periode_id', 'periode_id');
    }
}
