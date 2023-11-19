<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('send-otp', [OtpController::class, 'sendOtp']);
Route::post('verify-otp', [OtpController::class, 'verifyOTP']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware(['auth:api'])->group(function () {
    Route::middleware(['society_admin'])->group(function () {
        Route::get('users', [UserController::class, 'index']);
        Route::post('verify-user', [UserController::class, 'verifyUser']);
    });
    Route::get('user/auth', [UserController::class, 'authUser']);
    Route::resource('posts', PostController::class)->except(['create']);
    Route::post('posts/{post}/like', [PostController::class, 'like']);
    Route::resource('posts/{post}/comments', CommentController::class)->only(['index', 'store','destroy']);
    Route::post('upload/image', [ImageController::class, 'upload']);
});
