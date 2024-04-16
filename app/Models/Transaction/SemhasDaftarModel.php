<?php

namespace App\Models\Transaction;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SemhasDaftarModel extends AppModel
{
    use SoftDeletes;

    protected $table = 't_semhas_daftar';
    protected $primaryKey = 'semhas_daftar_id';

    protected static $_table = 't_semhas_daftar';
    protected static $_primaryKey = 'semhas_daftar_id';

    protected $fillable = [
        'pembimbing_dosen_id',
        'instruktur_lapangan_id',
        'semhas_id',
        'magang_id',
        'dosen_pembahas_id',
        'tanggal_daftar',
        'Judul',
        'link_github',
        'link_laporan',
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

    public function magang()
    {
        return $this->belongsTo('App\Models\Transaction\Magang', 'magang_id', 'magang_id');
    }

    public function semhas()
    {
        return $this->belongsTo('App\Models\Master\SemhasModel', 'semhas_id', 'semhas_id');
    }

    public function dosenPembahas()
    {
        return $this->belongsTo('App\Models\Master\DosenModel', 'dosen_pembahas_id', 'dosen_pembahas_id');
    }
}
