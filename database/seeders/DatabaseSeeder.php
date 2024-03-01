<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            //SDateSeeder::class,
            SGroupSeeder::class,
            MJurusanSeeder::class,
            MProdiSeeder::class,
            SUserSeeder::class,
            MMahasiswaSeeder::class,
            SMenuSeeder::class,
            SGroupMenuSeeder::class,
            MProgramSeeder::class,
            MKegiatanSeeder::class,
            MPeriodeSeeder::class,
            MWilayahSeeder::class,
            DMitraSeeder::class,
        ]);
    }
}
