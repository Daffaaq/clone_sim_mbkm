<?php

namespace App\Models\View;

use App\Models\AppModel;

class UserView extends AppModel
{
    protected $table = 'vw_user';
    protected $primaryKey = 'user_id';
    protected $uniqueKey = 'user_code';

    protected static $_table = 'vw_user';
    protected static $_primaryKey = 'user_id';
    protected static $_uniqueKey = 'user_code';

    protected $fillable = []; // data view tidak dapat di insert, hanya di select
}
