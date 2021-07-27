<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TheaterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Theater::factory(25)->create();
    }
}
