<?php

namespace App\Models\Transaction;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RevisiPembimbingDosenModel extends AppModel
{
    use SoftDeletes;

    protected $table = 't_revisi_pembimbing_dosen';
    protected $primaryKey = 'revisi_pembimbing_dosen_id';

    protected static $_table = 't_revisi_pembimbing_dosen';
    protected static $_primaryKey = 'revisi_pembimbing_dosen_id';

    protected $fillable = [
        'saran_pembimbing_dosen',
        'catatan_pembimbing_dosen',
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
}
