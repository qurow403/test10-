<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FirebaseAuthController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PostController;


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

Route::post('/auth/verify', [FirebaseAuthController::class, 'verifyToken']);

Route::middleware(['firebase.auth'])->group(function () {
    Route::get('/posts', [FeedController::class, 'index']);
    Route::post('/posts', [FeedController::class, 'store']);

    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::post('/posts/{id}/like', [PostController::class, 'toggleLike']);
    Route::put('/posts/{id}', [PostController::class, 'addComment']);
});

