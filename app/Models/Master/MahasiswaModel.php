<?php

namespace App\Models\Master;

use App\Models\AppModel;
use App\Models\Transaction\Magang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MahasiswaModel extends AppModel
{
    use SoftDeletes;

    protected $table = 'm_mahasiswa';
    protected $primaryKey = 'mahasiswa_id';

    protected static $_table = 'm_mahasiswa';
    protected static $_primaryKey = 'mahasiswa_id';

    protected $fillable = [
        'prodi_id',
        'user_id',
        'nim',
        'nama_mahasiswa',
        'email_mahasiswa',
        'no_hp',
        'jenis_kelamin',
        'kelas',
        'nama_ortu',
        'hp_ortu',
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
        // 'App\Models\Master\DosenModel' => 'jurusan_id'
    ];

    public function prodi()
    {
        return $this->belongsTo(ProdiModel::class, 'prodi_id', 'prodi_id');
    }
    // public function magang()
    // {
    //     return $this->hasMany('App\Models\Transaction\Magang', 'mahasiswa_id', 'mahasiswa_id');
    // }
}
