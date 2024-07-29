<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Signature;
use App\Models\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class SignatureController extends Controller
{

public function store(Request $request)
{
    // Validate user data
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

    // Validate image data
    $validatedDataImage = $request->validate([
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'company_logo1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'company_logo2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'gif' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $user = Auth::user();

    if (!$user) {
        Log::error('User is not authenticated.');
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }



    $signature = Signature::updateOrCreate(
        ['user_id' => $user->id],
        $validatedDataUser
    );

    // Handle image uploads
    $imagePaths = [];

    foreach (['image', 'company_logo', 'company_logo1', 'company_logo2', 'gif'] as $field) {
        if ($request->hasFile($field)) {
            $path = $request->file($field)->store('images', 'public');
            $imagePaths[$field] = $path;  
        } else {
            $imagePaths[$field] = null;  
        }
    }

    $image = Image::updateOrCreate(
        ['user_id' => $user->id],
        $imagePaths
    );

    // Log success message
    Log::info('Signature saved successfully.', ['user_id' => $user->id, 'signature_id' => $signature->id, 'image_id' => $image->id]);

    return response()->json([
        'message' => 'User information saved successfully',
        'user' => $user,
        'signature' => $signature,
        'image' => $image
    ], 200);
}


public function index()
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!$user->is_admin) {
            return response()->json(['message' => 'Forbidden. Admins only.'], 403);
        }

        $signatures = Signature::paginate(10);

        $formattedSignatures = $signatures->map(function ($signature) {
            return [
                'id' => $signature->id,
                'user_id' => $signature->user_id,
                'name' => $signature->name,
                'last_name' => $signature->last_name,
                'email' => $signature->email,
                'title' => $signature->title,
                'company' => $signature->company,
                'meeting_link' => $signature->meeting_link,
                'address' => $signature->address,
                'website' => $signature->website,
                'linkedin_profile' => $signature->linkedin_profile,
                'company_linkedin' => $signature->company_linkedin,
                'facebook' => $signature->facebook,
                'feedback' => $signature->feedback,
                'twitter' => $signature->twitter,
                'instagram' => $signature->instagram,
                'phone' => $signature->phone,
                'description' => $signature->description,
                'image' => $signature->image,
                'created_at' => $signature->created_at->toDateTimeString(),
                'updated_at' => $signature->updated_at->toDateTimeString(),
            ];
        });

        $responseData = [
            'message' => 'Signatures retrieved successfully.',
            'signatures' => $formattedSignatures,
        ];

        return response()->json($responseData, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to retrieve signatures', 'message' => $e->getMessage()], 500);
    }
}
public function show($id)
{
    try {

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $isAdmin = $user->is_admin;

        if (!$isAdmin && $user->id != $id) {
            return response()->json(['message' => 'Unauthorized to view this signature.'], 403);
        }

        $signature = Signature::where('user_id', $id)->first();

        if (!$signature) {
            return response()->json(['message' => 'Signature not found.'], 404);
        }

        return response()->json([
            'message' => 'Signature retrieved successfully.',
            'signature' => $signature
        ], 200);

    } catch (\Exception $e) {
        // Log the exception message and return an error response
        Log::error('Failed to retrieve signature', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Failed to retrieve signature', 'message' => $e->getMessage()], 500);
    }
}



    
    
    public function update(Request $request, $id)
    {
        Log::info('Request Data:', $request->all());
        
    
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
            'description' => 'required|string',
        ]);
    
        $validatedDataImage = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gif' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $isAdmin = $user->is_admin;

        // If not an admin, ensure that the user can only update their own information
        if (!$isAdmin && $user->id != $id) {
            return response()->json(['message' => 'Unauthorized to update this user.'], 403);
        }
    

        $imagePaths = [];
        foreach (['image', 'company_logo', 'company_logo1', 'company_logo2', 'gif'] as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('images', 'public');
                $imagePaths[$field] = basename($path);
            }
        }
    
        $signature = Signature::updateOrCreate(
            ['user_id' => $user->id],
            array_merge(
                $validatedDataUser,  
                $imagePaths  
            )
        );

        $image = Image::updateOrCreate(
            ['user_id' => $user->id],
            $imagePaths
        );
    
        return response()->json([
            'message' => 'User information updated successfully',
            'user' => $user,
            'signature' => $signature,
            'image' => $image
            
        ], 200);
    }
    
    
    // Add this at the top of your file

//     public function destroy($id)
// {
//     $user = Auth::user();

//     // Find the signature record for the authenticated user
//     $signature = Signature::where('user_id', $user->id)->find($id);

//     if (!$signature) {
//         return response()->json(['message' => 'Signature not found'], 404);
//     }

//     // Define the image fields
//     $imageFields = ['image', 'company_logo', 'company_logo1', 'company_logo2', 'gif'];

//     // Delete associated images from storage
//     foreach ($imageFields as $field) {
//         $filePath = $signature->$field;

//         if ($filePath) {
//             $fullPath = 'images/' . $filePath;

//             if (Storage::disk('public')->exists($fullPath)) {
//                 Storage::disk('public')->delete($fullPath);
//             }
//         }
//     }

//     $signature->delete();

//     return response()->json(['message' => 'Signature and associated images deleted successfully'], 200);
// }


public function destroy($id)
{
    $user = Auth::user();

    // Find the signature record for the authenticated user
    $signature = Signature::where('user_id', $user->id)->find($id);

    if (!$signature) {
        return response()->json(['message' => 'Signature not found'], 404);
    }

    $imageFields = ['image', 'company_logo', 'company_logo1', 'company_logo2', 'gif'];

    foreach ($imageFields as $field) {
        // Get the file path from the signature
        $filePath = $signature->$field;

        if (!empty($filePath) && Storage::disk('public')->exists('images/' . $filePath)) {
            Storage::disk('public')->delete('images/' . $filePath);
        }

        $signature->$field = null;
    }

    $signature->save();

    $signature->delete();

    return response()->json(['message' => 'Signature and associated images deleted successfully'], 200);
}



}