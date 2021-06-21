<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\AuthController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => 'loggedIn'], function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('me', [AuthController::class, 'me']);

        Route::post('register', [AuthController::class, 'register']);
        Route::post('forgot-password', [AuthController::class, 'sendPasswordResetEmail'])->name('forgot');
        Route::get('reset-password', [AuthController::class, 'resetPassword'])->name('reset');
//        Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset');
    });

    Route::group(['middleware' => 'auth:admin'], function () {
        // Resource Admin
        Route::group(['prefix' => 'admin'], function () {
            Route::get('all', [AdminController::class, 'index']);
            Route::post('create', [AdminController::class, 'store']);
            Route::post('update/{id}', [AdminController::class, 'update']);
            Route::put('switch-ban-at', [AdminController::class, 'switchBanAt']);
        });

        // Resource User
        Route::group(['prefix' => 'user'], function () {
            Route::get('all', [UserController::class, 'index']);
//            Route::post('store', [AuthController::class, 'store']);
            Route::get('show/{id}', [UserController::class, 'show']);
            Route::post('update/{id}', [UserController::class, 'update']);
            Route::post('search', [UserController::class, 'searchByName']);

            Route::put('switch-ban-at', 'UserController@switchBanAt');
            Route::put('switch-delete-at', 'UserController@switchDeleteAt');
        });

    });

});
