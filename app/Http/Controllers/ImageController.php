<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    public function store(Request $request, $userId)
    {
        // dd($request->all());
        $request->validate([
            // 'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo1' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo2' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'gif' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        // dd($filePath);
        // $user->image = $filePath;
        $image = new Image();
        $image =  Image::updateOrCreate(['user_id' => auth()->id()]);
        $image->user_id = auth()->id();
        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->store('images', 'public');
            $image->image = $filePath;
        }
        if ($request->hasFile('company_logo')) {
            $filePath = $request->file('company_logo')->store('images', 'public');
            $image->company_logo = $filePath;
        }
        if ($request->hasFile('company_logo1')) {
            $filePath = $request->file('company_logo1')->store('images', 'public');
            $image->company_logo1 = $filePath;
        }
        if ($request->hasFile('company_logo2')) {
            $filePath = $request->file('company_logo2')->store('images', 'public');
            $image->company_logo2 = $filePath;
        }
        if ($request->hasFile('gif')) {
            $filePath = $request->file('gif')->store('images', 'public');
            $image->gif = $filePath;
        }



        $image->save();


        try {
            // Ensure only the authenticated user can update their own images
            if (Auth::id() != $userId) {
                return response()->json(['error' => 'Unauthorized. You can only update your own images.'], 403);
            }

            $user = User::findOrFail($userId);

            // Handle image uploads
            // $this->handleImageUpload($request, $user, 'image');
            // $this->handleImageUpload($request, $user, 'company_logo');
            // $this->handleImageUpload($request, $user, 'company_logo1');
            // $this->handleImageUpload($request, $user, 'company_logo2');
            // $this->handleImageUpload($request, $user, 'gif');

            // Save the user model after updating images
            $user->save();

            Log::info('Images uploaded successfully for user: ' . $user->id);

            return response()->json(['message' => 'Images uploaded successfully', 'user' => $user], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('User not found with ID: ' . $userId);
            return response()->json(['error' => 'User not found'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to upload images: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload images. Please try again later.'], 500);
        }
    }

    public function update(Request $request, $userId)
    {
        try {
            $validatedData = $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'company_logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'company_logo1' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'company_logo2' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'gif' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Ensure only the authenticated user can update their own images
            if (Auth::id() != $userId) {
                return response()->json(['error' => 'Unauthorized. You can only update your own images.'], 403);
            }

            $user = User::findOrFail($userId);

            // Handle image updates
            $this->handleImageUpdate($request, $user, 'image');
            $this->handleImageUpdate($request, $user, 'company_logo');
            $this->handleImageUpdate($request, $user, 'company_logo1');
            $this->handleImageUpdate($request, $user, 'company_logo2');
            $this->handleImageUpdate($request, $user, 'gif');

            // Save the user model after updating images
            $user->save();

            Log::info('Images updated successfully for user: ' . $user->id);

            return response()->json(['message' => 'Images updated successfully', 'user' => $user], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update images'], 500);
        }
    }

    public function destroy($userId)
    {
        try {

            $user = User::findOrFail($userId);

            // Delete images and reset image paths
            $this->deleteImageAndResetField($user, 'image');
            $this->deleteImageAndResetField($user, 'company_logo');
            $this->deleteImageAndResetField($user, 'company_logo1');
            $this->deleteImageAndResetField($user, 'company_logo2');
            $this->deleteImageAndResetField($user, 'gif');

            // Save the user model after deleting images
            $user->save();

            Log::info('Images deleted successfully for user: ' . $user->id);

            return response()->json(['message' => 'Images deleted successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete images'], 500);
        }
    }

 


    // Helper method to handle image upload and update
    private function handleImageUpload(Request $request, User $user, $fieldName)
    {
        if ($request->hasFile($fieldName)) {
            $filePath = $request->file($fieldName)->store('images', 'public');
            // dd($filePath);
            // $user->image = $filePath;
            $image = new Image();
            $image->user_id = auth()->id();
            $image->filename = $filePath;
            $image->save();
        }
    }

    // Helper method to handle image update
    private function handleImageUpdate(Request $request, User $user, $fieldName)
    {
        if ($request->hasFile($fieldName)) {
            // Delete old image if exists
            if ($user->$fieldName) {
                Storage::delete('public/' . $user->$fieldName);
            }
            // Upload new image
            $filePath = $request->file($fieldName)->store('images', 'public');
            $user->$fieldName = $filePath;
        }
    }

    // Helper method to delete image and reset field
    private function deleteImageAndResetField(User $user, $fieldName)
    {
        // dd($fieldName);
        if ($user->$fieldName) {
            Storage::delete('public/' . $user->$fieldName);
            $user->$fieldName = null;
        }
    }
}
