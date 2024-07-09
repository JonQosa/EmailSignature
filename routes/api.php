<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('users', [UserController::class, 'store']);Route::put('users/{id}', [UserController::class, 'update']);
Route::put('users/{id}', [UserController::class, 'update']);
Route::delete('users/{id}', [UserController::class, 'destroy']);
Route::post('users/{userId}/images', [ImageController::class, 'store']);
Route::put('users/{userId}/images/{id}', [ImageController::class, 'update']);
Route::delete('users/{userId}/images/{id}', [ImageController::class, 'destroy']);

