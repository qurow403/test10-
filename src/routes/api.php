<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware(['firebase.auth'])->group(function () {
    Route::get('/', [FeedController::class, 'index']);
    Route::post('/', [FeedController::class, 'store']);

    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::post('/posts/{id}', [PostController::class, 'toggleLike']);
    Route::put('/posts/{id}', [PostController::class, 'addComment']);
});
