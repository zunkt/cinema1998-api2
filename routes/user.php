<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\User\AuthController;
use Illuminate\Support\Facades\Route;

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
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('register', [AuthController::class, 'register']);

    //Forgot by email
    Route::post('forgot-password', [AuthController::class, 'sendPasswordResetEmail'])->name('forgot');
    Route::get('reset-password', [AuthController::class, 'resetPassword'])->name('reset');
//        Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset');
});

Route::group(['middleware' => 'auth:user'], function () {
    /// Resource User
    Route::group(['prefix' => 'user'], function () {
        Route::put('switch-withdrawal-at', 'UserController@switchWithdrawalAt');
        Route::post('update', 'UserController@update');
        Route::post('withdrawal', 'UserController@withdrawal');
    });
});

