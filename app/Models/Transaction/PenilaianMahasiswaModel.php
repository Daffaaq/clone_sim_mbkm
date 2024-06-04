<?php

namespace App\Models\Transaction;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenilaianMahasiswaModel extends AppModel
{
    use SoftDeletes;

    protected $table = 't_penilaian_mahasiswa';
    protected $primaryKey = 'penilaian_mahasiswa_id';

    protected static $_table = 't_penilaian_mahasiswa';
    protected static $_primaryKey = 'penilaian_mahasiswa_id';

    protected $fillable = [
        'mahasiswa_id',
        'pembimbing_dosen_id',
        'instruktur_lapangan_id',
        'komentar_dosen_pembimbing',
        'komentar_instruktur_lapangan',
        'nilai_dosen_pembimbing',
        'nilai_instruktur_lapangan',
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

    public function pembimbingDosen()
    {
        return $this->belongsTo('App\Models\Transaction\PembimbingDosenModel', 'pembimbing_dosen_id', 'pembimbing_dosen_id');
    }

    public function instrukturLapangan()
    {
        return $this->belongsTo('App\Models\Transaction\InstrukturLapanganModel', 'instruktur_lapangan_id', 'instruktur_lapangan_id');
    }

    public function periode()
    {
        return $this->belongsTo('App\Models\Master\PeriodeModel', 'periode_id', 'periode_id');
    }
}
