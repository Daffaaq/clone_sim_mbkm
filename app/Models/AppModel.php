<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


/**
 * App\Models\AppModel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AppModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppModel query()
 * @mixin \Eloquent
 */
class AppModel extends Model
{
    protected $fillable = [];
    protected $uniqueKey  = '';

    private static $_deleteMessage = 'Data berhasil dihapus.';
    private static $_queryMessage = 'Data berhasil disimpan.';

    public static function setUniqueInsert(){
        return (static::$_uniqueKey)? Rule::unique(static::$_table, static::$_uniqueKey) : '';
    }

    public static function setUniqueInsertIgnore($id){
        return (static::$_uniqueKey)? Rule::unique(static::$_table, static::$_uniqueKey)->ignore($id, static::$_primaryKey) : '';
    }

    public static function insertData($request, $exception = []){
        $exception = array_merge($exception, ['_token', '_method']);
        $data = $request->except($exception);
        $data['created_by'] = Auth::user()->user_id;
        $data['created_at'] = date('Y-m-d H:i:s');

        return self::insert($data);
    }

    public static function updateData($id, $request, $exception = []){
        $exception = array_merge($exception, ['_token', '_method']);
        $data = $request->except($exception);
        $data['updated_by'] = Auth::user()->user_id;
        $data['updated_at'] = date('Y-m-d H:i:s');

        return self::where(static::$_primaryKey, $id)
            ->update($data);
    }

    public static function deleteData($id){
        $data['deleted_by'] = Auth::user()->user_id;
        $data['deleted_at'] = date('Y-m-d H:i:s');

        $goDelete = true;

        if(count(static::$childModel) > 0){
            foreach (static::$childModel as $model=>$columnFK){
                // check if Delete Cascade (is true) then delete all child data

                if(! static::$cascadeDelete) {
                    // check if child data exist
                    $goDelete = !(($model::where($columnFK, $id)->whereNull('deleted_at')->count() > 0));
                }

                if(!$goDelete){
                    break;
                }
            }

            if($goDelete){
                foreach (static::$childModel as $model=>$columnFK){
                    // delete all child data
                    $model::where($columnFK, $id)->whereNull('deleted_at')->update($data);
                }
            } else {
                self::$_deleteMessage = 'Data tidak dapat dihapus karena digunakan oleh data/tabel lain.';
                return false;
            }
        }

        return self::where(static::$_primaryKey, $id)->update($data);
    }

    public static function getDeleteMessage(){
        return self::$_deleteMessage;
    }

    public static function setMessage($message){
        self::$_queryMessage = $message;
    }

    public static function getMessage(){
        return self::$_queryMessage;
    }
}
