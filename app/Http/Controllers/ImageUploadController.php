<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;

class ImageUploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048', 
        ]);

        $imagePath = $request->file('image')->store('images'); // Store the image

        $image = new Image();
        $image->user_id = auth()->id(); 
        $image->filename = $imagePath;
        $image->save();

        return back()->with('success', 'Image uploaded successfully.');
    }
}

