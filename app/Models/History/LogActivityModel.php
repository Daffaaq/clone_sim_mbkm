<?php

namespace App\Models\History;

use App\Models\AppModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class LogActivityModel extends AppModel
{

    protected $table = 'h_activity';
    protected $primaryKey = 'id';
    protected $uniqueKey = '';

    protected static $_table = 'h_activity';
    protected static $_primaryKey = 'id';
    protected static $_uniqueKey = '';

    protected $fillable = [
        'user_id',
        'group_id',
        'level_id',
        'activity_scope',
        'activity_detail',
        'activity_at',
    ];

    protected static $cascadeDelete = false;   //  True: Force Delete from Parent (cascade)
    protected static $childModel = [
        //  Model => columnFK
    ];

    public static function setLog($user_id, $scope = '', $detail = ''){
        if(is_array($detail)){
            $detail = json_encode($detail);
        }

        DB::statement('CALL sp_log_activity(?, ?, ?)', [$user_id, $scope, $detail]);
    }
}
