<?php

use App\Http\Controllers\Api\ApiBlogController;
use App\Http\Controllers\Api\ApiCategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/blogs', [ApiBlogController::class, 'index']);
    Route::get('/blogs/{blog}', [ApiBlogController::class, 'show']);
    Route::post('/blogs', [ApiBlogController::class, 'store']);
    Route::put('/blogs/{blog}', [ApiBlogController::class, 'update']);
    Route::delete('/blogs/{blog}', [ApiBlogController::class, 'destroy']);

    Route::get('/categories', [ApiCategoryController::class, 'index']);
    Route::post('/categories', [ApiCategoryController::class, 'store']);
    Route::get('/categories/{category}', [ApiCategoryController::class, 'show']);
    Route::put('/categories/{category}', [ApiCategoryController::class, 'update']);
    Route::delete('/categories/{category}', [ApiCategoryController::class, 'destroy']);
});
