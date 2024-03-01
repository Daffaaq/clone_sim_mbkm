<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VWUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $view = "create or replace view vw_user as
select 	u.user_id, u.group_id, u.prodi_id, u.username, u.email, u.name, u.password, u.avatar_url, u.avatar_dir, u.is_active, g.group_code, g.group_name
from 	s_user u
join 	s_group g on g.group_id = u.group_id
join 	m_prodi p on p.prodi_id = u.prodi_id;";

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
