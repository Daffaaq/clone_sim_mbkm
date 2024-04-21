<?php

namespace App\Models\Master;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NilaiPembahasDosenModel extends AppModel
{

    use SoftDeletes;
    protected $table = 'm_nilai_pembahas_dosen';
    protected $primaryKey = 'nilai_pembahas_dosen_id';

    protected static $_table = 'm_nilai_pembahas_dosen';
    protected static $_primaryKey = 'nilai_pembahas_dosen_id';

    protected $fillable = [
        'name_kriteria_pembahas_dosen',
        'bobot',
        'parent_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected static $cascadeDelete = false;   //  True: Force Delete from Parent (cascade)
    protected static $childModel = [
        //  Model => columnFK
        // 'App\Models\Master\DosenModel' => 'jurusan_id'
    ];

    /**
     * Get all of the subKriteria for the NilaiModel
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subKriteria()
    {
        return $this->hasMany(NilaiPembahasDosenModel::class, 'parent_id', 'nilai_pembahas_dosen_id');
    }
    public function parent()
    {
        return $this->belongsTo(NilaiPembahasDosenModel::class, 'parent_id', 'nilai_pembahas_dosen_id');
    }
}
