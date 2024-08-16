<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Signature;
use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use App\Models\Image;

class UserController extends Controller
{

    public function store(Request $request)
    {
// dd($request->all());
        
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
                'gif' => 'nullable|url',
            ]);

            $loggedInUserId = auth()->id();

            $user = User::find($loggedInUserId);


        
            $imgUser = User::firstOrCreate(['id'=>auth()->id()]);

            if ($request->hasFile('image')) {
// Deleting old images if they exist somewhere
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

        //removed the gif 
            // if ($request->hasFile('gif')) {
            //     $gifPath = $request->file('gif')->store('images', 'public');
            //     $imgUser->gif = basename($gifPath);
            // }

            $imgUser->save();
            $user->save();
            // updated
            if($user){
                return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
            } else {
                return response()->json(['message' => 'Failed to create the user'], 500);
            }

}
public function index()
{

    $user = Auth::user();

    $userModel = User::find($user->id);

    
    if (!$userModel || !$userModel->is_admin) {
        return response()->json(['error' => 'Unauthorized. Only admins can access this resource.'], 403);
    }
    $users = User::with('image')->get();
    return response()->json($users);
}
public function show($userId){
    try {
        // Find the user by ID
        $user = User::findOrFail($userId);

        $imgUser = Image::where('user_id', $userId)->get();
        $signature = Signature::where('user_id', $userId)->first();

        return response()->json([
            // 'user' => $user,
            'signature' => $signature,
            'image' => $imgUser,
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'error' => 'User not found.'
        ], 404); 
    }

}

public function update(Request $request, $userId)
{
    try {
        $user = User::findOrFail($userId);
        if (Admin::where('id', $request->user()->id)->exists() === false && $userId !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized. You can only update your own information or you are not an admin.'], 403);
        }
        // Validate user data
        $validatedUserData = $request->validate([
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

        // Validate image data
        $validatedImageData = $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo1' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo2' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'gif' => 'nullable|url',
        ]);

        if (!$request->has('email')) {
            $request->validate([
                'email' => 'email',
            ]);
            $validatedUserData['email'] =  $request->input('email');
        } else {
            unset($validatedUserData['email']);
            // If email is present, validate it
        }

        // Update user
        $user->update($validatedUserData);

        // Handle image update
        $imgUser = Image::where('user_id', $userId)->first();

        if ($request->hasFile('image')) {
            if ($imgUser && $imgUser->image) {
                Storage::delete('public/' . $imgUser->image);
            }

            $imagePath = $request->file('image')->store('images', 'public');
            $imgUser->image = basename($imagePath);
        }

        $fields = ['company_logo', 'company_logo1', 'company_logo2'];
        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                if ($imgUser && $imgUser->$field) {
                    Storage::delete('public/' . $imgUser->$field);
                }

                $filePath = $request->file($field)->store('images', 'public');
                $imgUser->$field = basename($filePath);
            }
        }

        // Save or update image record
        if ($imgUser) {
            $imgUser->user_id = $user->id;
            $imgUser->save();
        }

        // Return response
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->refresh(), // Ensure you fetch the latest data after update
            'image' => $imgUser ? asset('storage/' . $imgUser->image) : null,
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['error' => 'User not found'], 404);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to update user', 'message' => $e->getMessage()], 500);
    }
}





public function destroy(Request $request, $userId)
{
    try {
        // Ensure the authenticated user is the one requesting deletion
        $user = $request->user(); // Retrieve the authenticated user

        if (!$user) {
            throw ValidationException::withMessages([
                'message' => ['Unauthorized']
            ])->status(403);
        }

        // Check if the authenticated user ID matches the $userId provided
        if ($user->id != $userId) {
            throw ValidationException::withMessages([
                'message' => ['Unauthorized']
            ])->status(403);
        }

        // Find the user to delete
        $userToDelete = User::findOrFail($userId);

        // Delete the user
        $userToDelete->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['error' => 'User not found'], 404);
    } catch (\Exception $e) {
        // Handle other exceptions
        return response()->json(['error' => 'Failed to delete user', 'message' => $e->getMessage()], 500);
    }
}

}
