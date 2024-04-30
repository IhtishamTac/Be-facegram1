<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);
    });
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::resource('posts', PostController::class);
        Route::get('following', [FollowController::class, 'getFollowings']);
        Route::prefix('users')->group(function () {
            Route::post('{username}/follow', [FollowController::class, 'followUser']);
            Route::delete('{username}/unfollow', [FollowController::class, 'unfollowUser']);
            Route::put('{username}/accept', [UserController::class, 'accFollowRequest']);
            Route::get('{username}/followers', [UserController::class, 'getFollowers']);
            Route::get('', [UserController::class, 'getUserNotFollowed']);
            Route::get('{username}', [UserController::class, 'getDetailUser']);
        });
    });
});
