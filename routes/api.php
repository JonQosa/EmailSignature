<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/get-users', [UserController::class, 'index']);
Route::get('users/{id}', [UserController::class, 'show']);
Route::post('save-user', [UserController::class, 'store']);
Route::put('users/{id}', [UserController::class, 'update']);
Route::put('users/{id}', [UserController::class, 'update']);
Route::delete('users/{id}', [UserController::class, 'destroy']);
Route::post('users/{userId}/images', [ImageController::class, 'store']);
Route::put('users/{userId}/images/{id}', [ImageController::class, 'update']);
Route::delete('users/{userId}/images/{id}', [ImageController::class, 'destroy']);

