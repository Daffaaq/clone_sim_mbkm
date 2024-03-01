<?php

namespace App\Models\Setting;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class GroupModel extends AppModel
{
    use SoftDeletes;

    protected $table = 's_group';
    protected $primaryKey = 'group_id';
    protected $uniqueKey = 'group_code';

    protected static $_table = 's_group';
    protected static $_primaryKey = 'group_id';
    protected static $_uniqueKey = 'group_code';

    protected $fillable = [
        'group_code',
        'group_name',
        'is_active',
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

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'group_id', 'group_id');
    }

    public static function insertData($request, $exception = [])
    {
        $data = $request->except(['_token', '_method']);
        $data['created_by'] = Auth::user()->user_id;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['group_code'] = strtoupper($data['group_code']);

        return self::insert($data);     // return status insert data
    }

    public static function updateData($id, $request, $exception = [])
    {
        $data = $request->except(['_token', '_method']);
        $data['updated_by'] = Auth::user()->user_id;
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['group_code'] = strtoupper($data['group_code']);

        return self::where('group_id', $id)
            ->update($data);
    }
}
