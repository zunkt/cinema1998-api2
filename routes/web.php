<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // mass assg
    // lúc này Model chỉ tự fill đc NAME và PW
//    \App\Models\Admin::create([
//        'name' => 'name 1',
//        'email' => 'email 1',
//        'password' => Hash::make(123456),
//    ]);

//    \App\Models\User::create([
//        'name' => 'tiendang212',
//        'email' => 'tiendang212@gmail.com',
//        'phone' => '01234567891',
//        'phone_confirmation_token' => 1,
//        'status' => 1,
//        'ticket' => 1,
//        'no_show' => 1,
//        'birthday' => 1,
//        'nationality' => 1,
//        'gender' => 1
//    ]);
//    dd(\App\Models\Admin::all());
//    dd(\App\Models\User::all());
    return view('welcome');
});

Route::get('/render-password', function (Request $request) {
    return Hash::make('Adminb123#');
});
