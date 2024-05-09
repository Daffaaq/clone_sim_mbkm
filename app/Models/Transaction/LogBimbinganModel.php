<?php

namespace App\Models\Transaction;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogBimbinganModel extends AppModel
{
    use SoftDeletes;

    protected $table = 't_log_bimbingan';
    protected $primaryKey = 'log_bimbingan_id';

    protected static $_table = 't_log_bimbingan';
    protected static $_primaryKey = 'log_bimbingan_id';

    protected $fillable = [
        'pembimbing_dosen_id',
        'instruktur_lapangan_id',
        'status1',
        'status2',
        'tanggal',
        'topik_bimbingan',
        'foto',
        'jam_mulai',
        'jam_selesai',
        'nilai_pembimbing_dosen',
        'nilai_instruktur_lapangan',
        'tanggal_status_dosen',
        'tanggal_status_instruktur',
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

    public function pembimbingDosen()
    {
        return $this->belongsTo('App\Models\Transaction\PembimbingDosenModel', 'pembimbing_dosen_id', 'pembimbing_dosen_id');
    }

    public function instrukturLapangan()
    {
        return $this->belongsTo('App\Models\Master\InstrukturLapanganModel', 'instruktur_lapangan_id', 'instruktur_lapangan_id');
    }
    public function periode()
    {
        return $this->belongsTo('App\Models\Master\PeriodeModel', 'periode_id', 'periode_id');
    }
}
