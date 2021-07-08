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
            'email' => 'tiendang212@gmail.com',
            'full_Name' => 'TienDang',
            'password' => Hash::make('12345678'),
            'identityNumber' => '123123123123',
            'address' => '12 hoang hoa tham',
        ]);

        \App\Models\User::factory(10)->create();
    }
}
