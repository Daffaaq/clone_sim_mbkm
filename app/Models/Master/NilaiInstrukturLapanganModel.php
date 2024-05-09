<?php

namespace App\Models\Master;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NilaiInstrukturLapanganModel extends AppModel
{

    use SoftDeletes;
    protected $table = 'm_nilai_instruktur_lapangan';
    protected $primaryKey = 'nilai_instruktur_lapangan_id';

    protected static $_table = 'm_nilai_instruktur_lapangan';
    protected static $_primaryKey = 'nilai_instruktur_lapangan_id';

    protected $fillable = [
        'name_kriteria_instruktur_lapangan',
        'bobot',
        'parent_id',
        'periode_id',
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
        return $this->hasMany(NilaiInstrukturLapanganModel::class, 'parent_id', 'nilai_instruktur_lapangan_id');
    }
    public function parent()
    {
        return $this->belongsTo(NilaiInstrukturLapanganModel::class, 'parent_id', 'nilai_instruktur_lapangan_id');
    }
    public function periode()
    {
        return $this->belongsTo('App\Models\Master\PeriodeModel', 'periode_id', 'periode_id');
    }
}
