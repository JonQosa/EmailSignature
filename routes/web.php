<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageUploadController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/upload-image', [ImageUploadController::class, 'uploadImage'])->name('upload.image');
