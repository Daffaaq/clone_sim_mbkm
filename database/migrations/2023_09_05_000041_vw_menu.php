<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VWMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $view = "create or replace view vw_menu as
select 	m.menu_id, m.menu_scope, m.menu_code, m.menu_name, m.menu_url, m.menu_level, m.order_no, m.parent_id, m.class_tag, m.icon, m.is_active,
		p.menu_code as parent_code, p.menu_name as parent_name
from 	s_menu m
left join s_menu p on m.parent_id = p.menu_id ";

        DB::statement($view);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
