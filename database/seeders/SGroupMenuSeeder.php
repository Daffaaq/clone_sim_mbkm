<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SGroupMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menu_admin = [];
        $menu_koordinator = [];
        $menu_mahasiswa = [];
        for ($i = 1; $i <= 22; $i++) {
            $menu_admin[] = ['group_id'  => 1, 'menu_id'   => $i, 'c'   => 1, 'r'    => 1, 'u'   => 1, 'd' => 1];
        }
        //remove with menu_id 14 and 15
        $menu_admin = array_filter($menu_admin, function ($value) {
            return $value['menu_id'] != 14 && $value['menu_id'] != 15;
        });

        for ($i = 1; $i <= 12; $i++) {
            $menu_koordinator[] = ['group_id'  => 2, 'menu_id'   => $i, 'c'   => 1, 'r'    => 1, 'u'   => 1, 'd' => 1];
        }
        $menu_koordinator[] = ['group_id'  => 2, 'menu_id'   => 13, 'c'   => 1, 'r'    => 1, 'u'   => 1, 'd' => 1];
        $menu_koordinator[] = ['group_id'  => 2, 'menu_id'   => 19, 'c'   => 1, 'r'    => 1, 'u'   => 1, 'd' => 1];
        //remove id 6,7 in koordinator
        $menu_koordinator = array_filter($menu_koordinator, function ($value) {
            return $value['menu_id'] != 6 && $value['menu_id'] != 7;
        });

        $menu_mahasiswa[] = ['group_id'  => 4, 'menu_id'   => 1, 'c'   => 1, 'r'    => 1, 'u'   => 1, 'd' => 1];
        $menu_mahasiswa[] = ['group_id'  => 4, 'menu_id'   => 3, 'c'   => 1, 'r'    => 1, 'u'   => 1, 'd' => 1];
        // $menu_mahasiswa[] = ['group_id'  => 4, 'menu_id'   => 11, 'c'   => 1, 'r'    => 1, 'u'   => 1, 'd' => 1];
        for ($i = 14; $i <= 16; $i++) {
            $menu_mahasiswa[] = ['group_id'  => 4, 'menu_id'   => $i, 'c'   => 1, 'r'    => 1, 'u'   => 1, 'd' => 1];
        }
        $menu_mahasiswa[] = ['group_id'  => 4, 'menu_id'   => 19, 'c'   => 1, 'r'    => 1, 'u'   => 1, 'd' => 1];

        DB::table('s_group_menu')->insert($menu_admin);
        DB::table('s_group_menu')->insert($menu_koordinator);
        DB::table('s_group_menu')->insert($menu_mahasiswa);
    }
}
