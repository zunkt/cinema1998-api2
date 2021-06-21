<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Admin::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'full_name' => 'Dang Admin',
            'password' => Hash::make('12345678'),
        ]);

//        \App\Models\Admin::factory()->create([
//            'name' => 'TienDang',
//            'email' => 'tiendang212@gmail.com',
//            'full_name' => 'TienDang Admin',
//            'password' => Hash::make('12345678'),
//        ]);
//        \App\Models\Admin::factory(5)->create();
    }
}
