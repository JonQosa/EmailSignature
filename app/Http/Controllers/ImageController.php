<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ImageController extends Controller
{
    public function store(Request $request, $userId)
    {
        // Validate incoming request data
       $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo1' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo2' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'gif' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Find the user by ID
            $user = User::findOrFail($userId);

            // Handle main image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
                $user->image = $imagePath;
            }

            // Handle company logos upload
            if ($request->hasFile('company_logo')) {
                $companyLogoPath = $request->file('company_logo')->store('images', 'public');
                $user->company_logo = $companyLogoPath;
            }
            if ($request->hasFile('company_logo1')) {
                $companyLogo1Path = $request->file('company_logo1')->store('images', 'public');
                $user->company_logo1 = $companyLogo1Path;
            }
            if ($request->hasFile('company_logo2')) {
                $companyLogo2Path = $request->file('company_logo2')->store('images', 'public');
                $user->company_logo2 = $companyLogo2Path;
            }
            if ($request->hasFile('gif')) {
                $gifPath = $request->file('gif')->store('images', 'public');
                $user->company_logo3 = $gifPath;
            }

            // Save the user record
            $user->save();

            // Log success message
            Log::info('Images uploaded successfully for user: ' . $user->id);

            return response()->json(['message' => 'Images uploaded successfully', 'user' => $user], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Log user not found error
            Log::error('User not found with ID: ' . $userId);

            return response()->json(['error' => 'User not found'], 404);
        } catch (\Exception $e) {
            // Log generic error
            Log::error('Failed to upload images: ' . $e->getMessage());

            return response()->json(['error' => 'Failed to upload images. Please try again later.'], 500);
        }
    }

    public function update(Request $request, $userId)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'company_logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'company_logo1' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'company_logo2' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'gif' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $user = User::findOrFail($userId);

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($user->image) {
                    Storage::delete('public/' . $user->image);
                }

                // Store new image
                $imagePath = $request->file('image')->store('images', 'public');
                $user->image = $imagePath;
            }

            // Handle company logos update
            if ($request->hasFile('company_logo')) {
                $companyLogoPath = $request->file('company_logo')->store('images', 'public');
                $user->company_logo = $companyLogoPath;
            }
            if ($request->hasFile('company_logo1')) {
                $companyLogo1Path = $request->file('company_logo1')->store('images', 'public');
                $user->company_logo1 = $companyLogo1Path;
            }
            if ($request->hasFile('company_logo2')) {
                $companyLogo2Path = $request->file('company_logo2')->store('images', 'public');
                $user->company_logo2 = $companyLogo2Path;
            }
            if ($request->hasFile('gif')) {
                $companyLogo3Path = $request->file('gif')->store('images', 'public');
                $user->gif = $companyLogo3Path;
            }

            // Save the user record
            $user->save();

            // Log success message
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
            // Find the user by ID
            $user = User::findOrFail($userId);

            // Delete user's images (if they exist)
            if ($user->image) {
                Storage::delete('public/' . $user->image);
                $user->image = null;
            }
            if ($user->company_logo) {
                Storage::delete('public/' . $user->company_logo);
                $user->company_logo = null;
            }
            if ($user->company_logo1) {
                Storage::delete('public/' . $user->company_logo1);
                $user->company_logo1 = null;
            }
            if ($user->company_logo2) {
                Storage::delete('public/' . $user->company_logo2);
                $user->company_logo2 = null;
            }
            if ($user->gif) {
                Storage::delete('public/' . $user->gif);
                $user->gif = null;
            }

            // Save the user record
            $user->save();

            
            // Return 
            return response()->json(['message' => 'Images deleted successfully'], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete images'], 500);
        }
    }
}

