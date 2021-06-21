<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->create([
            'name' => 'TienDang',
            'email' => 'tiendang212@gmail.com',
            'full_name' => 'TienDang',
            'password' => Hash::make('12345678'),
        ]);

        \App\Models\User::factory(10)->create();
    }
}
