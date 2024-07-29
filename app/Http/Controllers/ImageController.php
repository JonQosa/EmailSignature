<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Image;
use App\Models\Admin;

class ImageController extends Controller
{

    public function store(Request $request, $id)
    {

        $validatedData = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gif' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        dd($validatedData);

        $user = Auth::user();
        if (!$user || ($user->id != $id && !Admin::where('user_id', $user->id)->exists())) {
            return response()->json(['error' => 'Unauthorized. You can only update your own images or if you are an admin.'], 403);
        }

        $userImage = Image::firstOrNew(['user_id' => $id]);

        foreach (['image', 'company_logo', 'company_logo1', 'company_logo2', 'gif'] as $field) {
            if ($request->hasFile($field)) {
                if ($userImage->$field) {
                    // Log the path being deleted
                    Log::info("Deleting old file: public/" . $userImage->$field);
                    Storage::delete('public/' . $userImage->$field);
                }
                $filePath = $request->file($field)->store('images', 'public');
                $userImage->$field = $filePath;
                Log::info("File stored: public/" . $filePath);
            }
        }

        $userImage->save();

        return response()->json([
            'message' => 'Images uploaded successfully',
            'user_image' => $userImage
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gif' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        if (!$user || ($user->id != $id && !Admin::where('user_id', $user->id)->exists())) {
            return response()->json(['error' => 'Unauthorized. You can only update your own images or if you are an admin.'], 403);
        }

        $userImage = Image::where('user_id', $id)->first();
        if (!$userImage) {
            return response()->json(['error' => 'UserImage not found'], 404);
        }

        foreach (['image', 'company_logo', 'company_logo1', 'company_logo2', 'gif'] as $field) {
            if ($request->hasFile($field)) {
                if ($userImage->$field) {
                    // Log the path being deleted
                    Log::info("Deleting old file: public/" . $userImage->$field);
                    Storage::delete('public/' . $userImage->$field);
                }
                $filePath = $request->file($field)->store('images', 'public');
                $userImage->$field = $filePath;
                Log::info("File stored: public/" . $filePath);
            }
        }

        $userImage->save();

        return response()->json([
            'message' => 'Images updated successfully',
            'user_image' => $userImage
        ], 200);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || ($user->id != $id && !Admin::where('user_id', $user->id)->exists())) {
            return response()->json(['error' => 'Unauthorized. You can only delete your own images or if you are an admin.'], 403);
        }

        $userImage = Image::where('user_id', $id)->first();
        if (!$userImage) {
            return response()->json(['error' => 'UserImage not found'], 404);
        }

        foreach (['image', 'company_logo', 'company_logo1', 'company_logo2', 'gif'] as $field) {
            if ($userImage->$field) {
                // Log the path being deleted
                Log::info("Deleting file: public/" . $userImage->$field);
                Storage::delete('public/' . $userImage->$field);
                $userImage->$field = null;
            }
        }

        $userImage->save();

        return response()->json([
            'message' => 'Images deleted successfully'
        ], 200);
    }
}
