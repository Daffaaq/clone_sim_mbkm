<?php

namespace App\Models\View;

use App\Models\AppModel;

class MenuView extends AppModel
{
    protected $table = 'vw_menu';
    protected $primaryKey = 'menu_id';
    protected $uniqueKey = 'menu_code';

    protected static $_table = 'vw_menu';
    protected static $_primaryKey = 'menu_id';
    protected static $_uniqueKey = 'menu_code';

    protected $fillable = [];

    protected static $childModel = [
        //  Model => Foreign Key Column
    ];
}
