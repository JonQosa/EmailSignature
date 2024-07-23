<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageUploadController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/upload-image', [ImageUploadController::class, 'uploadImage'])->name('upload.image');


Route::get('/test/{resource?}', function ($resource = null) {
    // return response()->json(['resource' => storage_path($resource)]);

    if ($resource && file_exists(storage_path($resource))) {
        $headers = [
            'x-words' => 'words',
            'Access-Control-Allow-Origin' => '',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
        ];
        $response = response()->file(storage_path($resource), $headers);
        return $response;
    }
    return response()->json(['resource' => $resource]);
})->where('resource', '.');
