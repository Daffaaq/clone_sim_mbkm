<?php

namespace App\Models\Master;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class DosenModel extends AppModel
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'm_dosen';
    protected $primaryKey = 'dosen_id';

    protected static $_table = 'm_dosen';
    protected static $_primaryKey = 'dosen_id';

    protected $fillable = [
        'dosen_nip',
        'dosen_nidn',
        'dosen_name',
        'dosen_email',
        'dosen_phone',
        'dosen_gender',
        'dosen_tahun',
        'kuota',
        'user_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'dosen_name',
                'dosen_email',
                'dosen_phone',
                'dosen_gender',
                'dosen_tahun',
                'dosen_nidn',
                'dosen_nip',
                'kuota',
                'user_id',
            ])
            ->useLogName('dosen');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Dosen record has been {$eventName}";
    }

    protected static $cascadeDelete = false; // True: Force Delete from Parent (cascade)
    protected static $childModel = [
        // Model => columnFK
        // 'App\Models\Master\DosenModel' => 'jurusan_id'
    ];

    public function pembimbingDosen()
    {
        return $this->hasMany('App\Models\Transaction\PembimbingDosenModel', 'dosen_id', 'dosen_id');
    }
}
