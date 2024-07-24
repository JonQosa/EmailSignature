<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Auth\UserAuthController;


Route::get('/user', function (Request $request) {

    return $request->user();

})->middleware('auth:sanctum');


Route::put('/users/{userId}', [UserController::class, 'update'])
    ->middleware('auth', 'check.user.ownership');

Route::get('get-users', [UserController::class, 'index'])->middleware('auth:sanctum');
Route::get('users/{id}', [UserController::class, 'show'])->middleware('auth:sanctum');
Route::put('save-user/{id}', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::put('/users/{userId}/update', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::post('/users/store', [UserController::class, 'store'])->middleware('auth:sanctum');
Route::delete('delete-signature/{id}', [UserController::class, 'destroy'])->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/users/{userId}/images', [ImageController::class, 'store']);
    Route::put('/users/{userId}/images', [ImageController::class, 'update']);
    Route::delete('/users/{userId}/images', [ImageController::class, 'destroy']);
});


// Admin routes
Route::post('admin/register', [AdminAuthController::class, 'register']);
Route::post('admin/login', [AdminAuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('admin/logout', [AdminAuthController::class, 'logout']);


// Specific User to register/login
Route::post('/login', [UserAuthController::class, 'login']);
Route::post('/register', [UserAuthController::class, 'register']);
Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');


