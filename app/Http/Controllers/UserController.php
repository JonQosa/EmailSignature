<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;
class UserController extends Controller
{
    public function store(Request $request)
    {
        
            // Validate incoming request data
            $validatedDataUser = $request->validate([
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'company' => 'required|string|max:255',
                'meeting_link' => 'nullable|url|max:255',
                'address' => 'required|string|max:255',
                'website' => 'nullable|url|max:255',
                'feedback' => 'nullable|string|max:255',
                'company_linkedin' => 'nullable|url|max:255',
                'linkedin_profile' => 'nullable|url',
                'facebook' => 'nullable|url',
                'twitter' => 'nullable|url',
                'instagram' => 'nullable|url',
                'phone' => 'required|string',
                'email' => 'required|email',
                'description' => 'required|string'
            ]);

            $validationDataImage = $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'company_logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'company_logo1' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'company_logo2' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'gif' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $user = User::create($validatedDataUser);

        
            $imgUser = new Image();

            if ($request->hasFile('image')) {
                // Delete old image if exists

                $imagePath = $request->file('image')->store('images', 'public');
                $imgUser->image = basename($imagePath);
            }

            if ($request->hasFile('company_logo')) {
                $companyLogoPath = $request->file('company_logo')->store('images', 'public');
                $imgUser->company_logo = basename($companyLogoPath);
            }
            if ($request->hasFile('company_logo1')) {
                $companyLogo1Path = $request->file('company_logo1')->store('images', 'public');
                $imgUser->company_logo1 = basename($companyLogo1Path);
            }
            if ($request->hasFile('company_logo2')) {
                $companyLogo2Path = $request->file('company_logo2')->store('images', 'public');
                $imgUser->company_logo2 = basename($companyLogo2Path);
            }
            if ($request->hasFile('gif')) {
                $gifPath = $request->file('gif')->store('images', 'public');
                $imgUser->gif = basename($gifPath);
            }

            $imgUser->user_id = $user->id;
            $imgUser->save();
            $user->save();
            // updated
            if($user){
                return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
            } else {
                return response()->json(['message' => 'Failed to create the user'], 500);
            }

}
public function update(Request $request, $userId)
{

    try {
        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'title' => 'string|max:255',
            'company' => 'string|max:255',
            'meeting_link' => 'nullable|url|max:255',
            'address' => 'string|max:255',
            'website' => 'nullable|url|max:255',
            'linkedin_profile' => 'nullable|url',
            'company_linkedin' => 'nullable|url|max:255',
            'facebook' => 'nullable|url',
            'feedback' => 'nullable|string|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url',
            'phone' => 'string',
            'email' => 'email',
            'description' => 'string',
        ]);
        $validationDataImage = $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo1' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo2' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'gif' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);



        $imgUser = Image::where('user_id', $userId)->first();


        if ($request->hasFile('image')) {
            if ($imgUser->image) {
                Storage::delete('public/' . $imgUser->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('images', 'public');
            $imgUser->image = basename($imagePath);
        }

        // Handle company logos update
        // dd($request->company_logo);
        if ($request->hasFile('company_logo')) {
            $companyLogoPath = $request->file('company_logo')->store('images', 'public');
            $imgUser->company_logo = basename($companyLogoPath);
        }
        if ($request->hasFile('company_logo1')) {
            $companyLogo1Path = $request->file('company_logo1')->store('images', 'public');
            $imgUser->company_logo1 = basename($companyLogo1Path);
        }
        if ($request->hasFile('company_logo2')) {
            $companyLogo2Path = $request->file('company_logo2')->store('images', 'public');
            $imgUser->company_logo2 = basename($companyLogo2Path);
        }
        if ($request->hasFile('gif')) {
            $gifPath = $request->file('company_logo3')->store('images', 'public');
            $imgUser->gif = basename($gifPath);
        }

        $user = User::findOrFail($userId);
        // dd($user);
        $imgUser->user_id = $user->id;
        $imgUser->save();

        // Update user attributes based on validated data
        $user->update($validatedData);
        // dd($validatedData);
        // Return success response
    return response()->json(['message' => 'User updated successfully', 'user' => $user->refresh()]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // Handle case where user with given ID is not found
        return response()->json(['error' => 'User not found'], 404);
    } catch (\Exception $e) {
        // Handle other exceptions
        return response()->json(['error' => 'Failed to update user', 'message' => $e->getMessage()], 500);
    }
}
public function destroy($userId)
{
    try {
        $user = User::findOrFail($userId);

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['error' => 'User not found'], 404);
    } catch (\Exception $e) {
        // Handle other exceptions
        return response()->json(['error' => 'Failed to delete user', 'message' => $e->getMessage()], 500);
    }
}

}