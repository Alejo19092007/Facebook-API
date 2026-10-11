<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::post('/posts/{id}/like', [PostController::class, 'like']);

Route::get('/posts/{id}/comments', [CommentController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/posts', [PostController::class, 'store']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    Route::post('/posts/{id}/comments', [CommentController::class, 'store']);
});
