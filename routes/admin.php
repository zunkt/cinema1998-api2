<?php

namespace App\Http\Controllers\Api\Admin;

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
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('me', [AuthController::class, 'me']);

    Route::post('register', [AuthController::class, 'register']);
    Route::post('forgot-password', [AuthController::class, 'sendPasswordResetEmail'])->name('forgot');
    Route::get('reset-password', [AuthController::class, 'resetPassword'])->name('reset');
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
        Route::get('show/{id}', [UserController::class, 'show']);
        Route::post('update/{id}', [UserController::class, 'update']);
        Route::post('search', [UserController::class, 'searchByName']);
        Route::post('delete/{id}', [UserController::class, 'destroy']);
    });

    // Resource Ticket
    Route::group(['prefix' => 'ticket'], function () {
        Route::get('all', [TicketController::class, 'index']);
        Route::get('show/{id}', [TicketController::class, 'show']);
        Route::post('store', [TicketController::class, 'store']);
        Route::post('update/{id}', [TicketController::class, 'update']);
        Route::post('delete/{id}', [TicketController::class, 'destroy']);
    });

    // Resource Schedule
    Route::group(['prefix' => 'schedule'], function () {
        Route::get('all', [ScheduleController::class, 'index']);
        Route::get('show/{id}', [ScheduleController::class, 'show']);
        Route::post('store', [ScheduleController::class, 'store']);
        Route::post('update/{id}', [ScheduleController::class, 'update']);
        Route::post('delete/{id}', [ScheduleController::class, 'destroy']);
    });

    // Resource Bill
    Route::group(['prefix' => 'bill'], function () {
        Route::get('all', [BillController::class, 'index']);
        Route::get('show/{id}', [BillController::class, 'show']);
        Route::post('store', [BillController::class, 'store']);
        Route::post('update/{id}', [BillController::class, 'update']);
        Route::post('delete/{id}', [BillController::class, 'destroy']);
    });

    // Resource FeedBack
    Route::group(['prefix' => 'feedback'], function () {
        Route::get('all', [FeedBackController::class, 'index']);
        Route::get('show/{id}', [FeedBackController::class, 'show']);
        Route::post('store', [FeedBackController::class, 'store']);
        Route::post('update/{id}', [FeedBackController::class, 'update']);
        Route::post('delete/{id}', [FeedBackController::class, 'destroy']);
    });

    // Resource Movie
    Route::group(['prefix' => 'movie'], function () {
        Route::get('all', [MovieController::class, 'index']);
        Route::get('show/{id}', [MovieController::class, 'show']);
        Route::post('store', [MovieController::class, 'store']);
        Route::post('update/{id}', [MovieController::class, 'update']);
        Route::post('delete/{id}', [MovieController::class, 'destroy']);
    });

    // Resource Theater
    Route::group(['prefix' => 'theater'], function () {
        Route::get('all', [TheaterController::class, 'index']);
        Route::get('show/{id}', [TheaterController::class, 'show']);
        Route::post('store', [TheaterController::class, 'store']);
        Route::post('update/{id}', [TheaterController::class, 'update']);
        Route::post('delete/{id}', [TheaterController::class, 'destroy']);
    });

    // Resource Room
    Route::group(['prefix' => 'room'], function () {
        Route::get('all', [RoomController::class, 'index']);
        Route::get('show/{id}', [RoomController::class, 'show']);
        Route::post('store', [RoomController::class, 'store']);
        Route::post('update/{id}', [RoomController::class, 'update']);
        Route::post('delete/{id}', [RoomController::class, 'destroy']);
    });

    // Resource Coupon
    Route::group(['prefix' => 'coupon'], function () {
        Route::get('all', [CouponController::class, 'index']);
        Route::get('show/{id}', [CouponController::class, 'show']);
        Route::post('store', [CouponController::class, 'store']);
        Route::post('update/{id}', [CouponController::class, 'update']);
        Route::post('delete/{id}', [CouponController::class, 'destroy']);
    });

    // Resource Faq
    Route::group(['prefix' => 'faq'], function () {
        Route::get('all', [FaqController::class, 'index']);
        Route::get('show/{id}', [FaqController::class, 'show']);
        Route::post('store', [FaqController::class, 'store']);
        Route::post('update/{id}', [FaqController::class, 'update']);
        Route::post('delete/{id}', [FaqController::class, 'destroy']);
    });

    // Resource Seat
    Route::group(['prefix' => 'seat'], function () {
        Route::get('all', [SeatController::class, 'index']);
        Route::get('show/{id}', [SeatController::class, 'show']);
        Route::post('store', [SeatController::class, 'store']);
        Route::post('update/{id}', [SeatController::class, 'update']);
        Route::post('delete/{id}', [SeatController::class, 'destroy']);
    });

    // Resource Seat Room
    Route::group(['prefix' => 'seatroom'], function () {
        Route::get('all', [SeatRoomController::class, 'index']);
        Route::get('show/{id}', [SeatRoomController::class, 'show']);
        Route::post('store', [SeatRoomController::class, 'store']);
        Route::post('update/{id}', [SeatRoomController::class, 'update']);
        Route::post('delete/{id}', [SeatRoomController::class, 'destroy']);
    });
});
