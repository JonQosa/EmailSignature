<?php
use App\Http\Controllers\AdminMetricsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\SignatureController;
Route::get('/signature/me', [SignatureController::class, 'signaturesShow'])->middleware('auth:sanctum');
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::put('/users/{userId}', [UserController::class, 'update'])
    ->middleware('auth', 'check.user.ownership');

Route::get('metrics', [AdminMetricsController::class, 'getMetrics'])->middleware('auth:sanctum');


Route::get('get-users', [UserController::class, 'index'])->middleware('auth:sanctum');
Route::get('users/{id}', [UserController::class, 'show'])->middleware('auth:sanctum');
Route::put('save-user/{id}', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::put('/users/{userId}/update', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::post('/users/store', [UserController::class, 'store'])->middleware('auth:sanctum');
Route::delete('delete-signature/{id}', [UserController::class, 'destroy'])->middleware('auth:sanctum');

// Route::get('signatures/{id}', [SignatureController::class, 'getSignatures'])->middleware('auth:sanctum');
Route::get('get-users', [UserAuthController::class, 'getAllUsers'])->middleware('auth:sanctum');
Route::delete('delete-user/{user_id}', [UserAuthController::class, 'deleteUserById'])->middleware('auth:sanctum');
// Route::get('/signatures/{userId}', [SignatureController::class, 'getUserSignatures']);


Route::get('signatures/{id}', [SignatureController::class, 'show'])->middleware('auth:sanctum');
Route::get('get-signatures', [SignatureController::class, 'index'])->middleware('auth:sanctum');
Route::post('/signature/store', [SignatureController::class, 'store'])->middleware('auth:sanctum');
Route::put('/signature-update/{id}', [SignatureController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/signature/{id}', [SignatureController::class, 'destroy'])->middleware('auth:sanctum');

// Route::post('/signatures/users/{id}', SignatureController::class,'siganaturesShow')->middleware('auth:sanctum');

Route::get('/user-images/{userId}', [ImageController::class, 'getImage'])->middleware('auth:sanctum');

// Route::get('/link-signatures/{userId}', [SignatureController::class, 'show'])
//     ->middleware('auth:api');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/users/{userId}/images', [ImageController::class, 'store']);
    Route::put('/users/{userId}/images', [ImageController::class, 'update']);
    Route::delete('/users/{userId}/images', [ImageController::class, 'destroy']);
});

Route::put('/signature/{id}/image', [ImageController::class, 'update'])->middleware('auth:sanctum');

Route::get('/signature-html/{id}', [SignatureController::class, 'getSignatureHtml']); // Add the missing semicolon here
// Route::get('/signature-html/{userId}', [SignatureController::class, 'showSignature']);
// Admin routes

// Specific User to register/login
Route::post('/login', [UserAuthController::class, 'login']);
Route::post('/register', [UserAuthController::class, 'register']);
Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');

Route::put('/admin/users/{userId}/role', [AdminAuthController::class, 'changeRole'])->middleware('auth:sanctum');
