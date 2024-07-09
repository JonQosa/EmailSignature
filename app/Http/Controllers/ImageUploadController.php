<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImageUser; // Make sure to import your ImageUser model
use App\Models\Image;

class ImageUploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048', // Example validation rules
        ]);

        $imagePath = $request->file('image')->store('images'); // Store the image

        // Save image information to image_user table
        $image = new Image();
        $image->user_id = auth()->id(); // Assuming you have authentication set up
        $image->filename = $imagePath;
        // Add other image details if needed
        $image->save();

        return back()->with('success', 'Image uploaded successfully.');
    }
}

