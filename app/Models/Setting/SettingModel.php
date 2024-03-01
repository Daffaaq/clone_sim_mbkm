<?php

namespace App\Models\Setting;

use App\Models\AppModel;
use App\Models\Master\SubjectModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Reference\MajorModel
 *
 * @property int $major_id
 * @property string|null $major_code Kode Jurusan
 * @property string|null $major_name Nama Jurusan
 * @property \Illuminate\Support\Carbon $created_at
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection|SubjectModel[] $subject
 * @property-read int|null $subject_count
 * @method static \Illuminate\Database\Eloquent\Builder|SettingModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingModel newQuery()
 * @method static \Illuminate\Database\Query\Builder|SettingModel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingModel whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingModel whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingModel whereMajorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingModel whereMajorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingModel whereMajorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingModel whereUpdatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|SettingModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SettingModel withoutTrashed()
 * @mixin \Eloquent
 */
class SettingModel extends AppModel
{
    use SoftDeletes;

    protected $table = 's_setting';
    protected $primaryKey = 'setting_id';
    protected $fillable = [
        'kode',
        'keterangan',
        'nilai',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public function updateData($id, $request){
        $data = $request->except(['_token', '_method']);
        $data['updated_by'] = Auth::user()->user_id;
        $data['updated_at'] = date('Y-m-d H:i:s');

        return self::where('major_id', $id)
            ->update($data);
    }
}
