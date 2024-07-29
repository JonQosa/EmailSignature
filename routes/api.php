<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\SignatureController;



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




Route::get('signatures/{id}', [SignatureController::class, 'show'])->middleware('auth:sanctum');
Route::get('get-signatures', [SignatureController::class, 'index'])->middleware('auth:sanctum');
Route::post('/signature/store', [SignatureController::class, 'store'])->middleware('auth:sanctum');
Route::put('/signature/{id}', [SignatureController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/signature/{id}', [SignatureController::class, 'destroy'])->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/users/{userId}/images', [ImageController::class, 'store']);
    Route::put('/users/{userId}/images', [ImageController::class, 'update']);
    Route::delete('/users/{userId}/images', [ImageController::class, 'destroy']);
});


Route::put('/signature/{id}/image', [ImageController::class, 'update'])->middleware('auth:sanctum');



// Admin routes
Route::post('admin/register', [AdminAuthController::class, 'register']);
Route::post('admin/login', [AdminAuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('admin/logout', [AdminAuthController::class, 'logout']);


// Specific User to register/login
Route::post('/login', [UserAuthController::class, 'login']);
Route::post('/register', [UserAuthController::class, 'register']);
Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');


Route::put('/admin/users/{userId}/role', [AdminAuthController::class, 'changeRole'])->middleware('auth:sanctum');
