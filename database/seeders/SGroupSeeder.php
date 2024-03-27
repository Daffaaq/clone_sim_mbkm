<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('s_group')->insert([
            ['group_id' => 1, 'group_code' => 'ADM', 'group_name' => 'Admin'],
            ['group_id' => 2, 'group_code' => 'KOM', 'group_name' => 'Koordinator MBKM'],
            ['group_id' => 3, 'group_code' => 'DSN', 'group_name' => 'Dosen'],
            ['group_id' => 4, 'group_code' => 'MHS', 'group_name' => 'Mahasiswa'],
        ]);
    }
}
