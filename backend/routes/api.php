<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TagController;

Route::apiResource('articles', ArticleController::class);
Route::apiResource('tags', TagController::class);

Route::get('articles/{article}/comments', [CommentController::class, 'index']);
Route::post('articles/{article}/comments', [CommentController::class, 'store']);
Route::delete('articles/{article}/comments/{comment}', [CommentController::class, 'destroy']);