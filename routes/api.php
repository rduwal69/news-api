<?php

use App\Http\Controllers\Api\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('articles', [ArticleController::class, 'index']);
Route::post('articles', [ArticleController::class, 'store']);
Route::get('articles/{id}', [ArticleController::class, 'show']);
Route::get('articles/{id}/edit', [ArticleController::class, 'edit']);
Route::post('articles/{id}/edit', [ArticleController::class, 'update']);
Route::delete('articles/{id}/delete', [ArticleController::class, 'delete']);



