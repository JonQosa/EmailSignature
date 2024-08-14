<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Signature;
use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class SignatureController extends Controller
{

    public function getUserSignatures($id)
    {
        // Ensure the user is authenticated
        $user = Auth::user();
    
        // Check if the authenticated user is trying to access their own signatures
        if ($user->id !== (int) $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        // Retrieve all signatures created by the user
        $signatures = Signature::where('user_id', $id)->get();
    
        // Return the signatures in the response
        return response()->json(['signatures' => $signatures]);
    }
    

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
            'gif' => 'nullable|url|max:255',
            'description' => 'required|string',
            'html_content' => 'string',
        ]);


        

        // Validate image data
        $validatedDataImage = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        

       $user = Auth::user();

        if (!$user) {
            Log::error('User is not authenticated.');
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $signature = Signature::create(
            ['user_id' => $user->id,
            'name' => $request->name,
            'last_name' => $request->last_name,
            'title' => $request->title,
            'company' => $request->company,
            'meeting_link' => $request->meeting_link,
            'address' => $request->address,
            'website' => $request->website,
            'feedback' => $request->feedback,
            'company_linkedin' => $request->company_linkedin,
            'linkedin_profile' => $request->linkedin_profile,
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'phone' => $request->phone,
            'email' => $request->email,
            'gif' => $request->gif,
            'description' => $request->description,
            'html_content' => $request->html_content,            
          ]
        );
        // $signature = Signature::updateOrCreate(
        //     ['user_id' => $user->id],
        //     $validatedDataUser
        // );

        // Handle image uploads
        $imagePaths = [];

        foreach (['image', 'company_logo', 'company_logo1', 'company_logo2'] as $field) {
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
    // public function getSignatureHtml($userId)
    // {
    //     // Find the signature for the user
    //     $signature = Signature::where('user_id', $userId)->first();
    
    //     if (!$signature) {
    //         return response()->json(['error' => 'Signature not found'], 404);
    //     }
    
    //     // Debugging: Check if content exists
    //     if (!$signature->content) {
    //         return response()->json(['error' => 'Signature content is empty'], 400);
    //     }
    
    //     // Fetch images related to the user
    //     $userImages = Image::where('user_id', $userId)->first();
    
    //     // Prepare the image URLs array
    //     $imageUrls = [
    //         'image' => $userImages && $userImages->image ? asset('storage/uploads/' . $userImages->image) : null,
    //         'company_logo' => $userImages && $userImages->company_logo ? asset('storage/uploads/' . $userImages->company_logo) : null,
    //         'company_logo1' => $userImages && $userImages->company_logo1 ? asset('storage/uploads/' . $userImages->company_logo1) : null,
    //         'company_logo2' => $userImages && $userImages->company_logo2 ? asset('storage/uploads/' . $userImages->company_logo2) : null,
    //         'gif' => $userImages && $userImages->gif ? asset('storage/uploads/' . $userImages->gif) : null,
    //     ];
    
    //     // Return the signature HTML content and image URLs
    //     return response()->json([
    //         'html_content' => $signature,
    //         'images' => $imageUrls
    //     ]);
    // }
    
    
    
    public function getSignatureHtml($id)
    {
        $signature = Signature::where('id', $id)->first();
        $userImages = Image::where('user_id', $signature->user_id)->first();

        if (!$signature) {
            return response()->json(['error' => 'Signature not found'], 404);
        }

        $imageUrls = [
            'image' => $userImages->image ? asset('storage/uploads/' . $userImages->image) : null,
            'company_logo' => $userImages->company_logo ? asset('storage/uploads/' . $userImages->company_logo) : null,
            'company_logo1' => $userImages->company_logo1 ? asset('storage/uploads/' . $userImages->company_logo1) : null,
            'company_logo2' => $userImages->company_logo2 ? asset('storage/uploads/' . $userImages->company_logo2) : null,
        ];

        return response()->json([
        'html_content' => $signature,
        'image' => $userImages,
    ]);
    }

    public function index(Request $request)
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!$user->is_admin) {
            return response()->json(['message' => 'Forbidden. Admins only.'], 403);
        }

        // Fetch paginated list of signatures
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
                'gif' => $signature->gif,
                'instagram' => $signature->instagram,
                'phone' => $signature->phone,
                'description' => $signature->description,
                'image' => $signature->image,
                'created_at' => $signature->created_at->toDateTimeString(),
                'updated_at' => $signature->updated_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'message' => 'Signatures retrieved successfully.',
            'signatures' => $formattedSignatures,
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to retrieve signatures', 'message' => $e->getMessage()], 500);
    }
}

// public function getSignatures($userId)
//     {
//         // Fetch signatures
//         $signatures = Signature::where('user_id', $userId)->get();

//         // Fetch associated images
//         foreach ($signatures as $signature) {
//             $image = UserImage::where('user_id', $userId)->first();
//             if ($image) {
//                 $signature->image = $image; // Attach the image to the signature
//             }
//         }

//         return response()->json(['signatures' => $signatures]);
//     }t
public function signaturesShow(Request $request){
    $user = Auth::user();
    $signatures = Signature::where('user_id', $user->id)->get();


    return response()->json([
        'signatures' => $signatures,
        
    ]);
}



    public function show(Request $request, $id)
    {

        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // $isAdmin = $user->is_admin;

            // if (!$isAdmin && $user->id != $id) {
            //     return response()->json(['message' => 'Unauthorized to view this signature.'], 403);
            // }

            $signature = Signature::where('id', $id)->first();

            if (!$signature) {
                return response()->json(['message' => 'Signature not found.'], 404);
            }

            $image =  Image::where('user_id', $signature->user_id)->get();

            return response()->json([
                'message' => 'Signature retrieved successfully.',
                'signature' => $signature,
               'image' => $image,
        

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
        $currentUser = Auth::user();
        $signature = Signature::find($id);
        $user = User::findOrFail($signature->user_id);

    if (! $currentUser->isAdmin() && $currentUser->id !== $signature->user_id) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

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
            'gif' => 'nullable|url',
            'description' => 'required|string',
            'html_content' => 'string'

        ]);

        $validatedDataImage = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // $updatedSignature = Signature::Create([
        //     'user_id' => $user->id,
        //     'name' => $request->name,
        //     'last_name' => $request->last_name,
        //     'title' => $request->title,
        //     'company' => $request->company,
        //     'meeting_link' => $request->meeting_link,
        //     'address' => $request->address,
        //     'website' => $request->website,
        //     'feedback' => $request->feedback,
        //     'company_linkedin' => $request->company_linkedin,
        //     'linkedin_profile' => $request->linkedin_profile,
        //     'facebook' => $request->facebook,
        //     'twitter' => $request->twitter,
        //     'phone' => $request->phone,
        //     'email' => $request->email,
        //     'gif' => $request->gif,
        //     'description' => $request->description,
        //     'html_content' => $request->html_content,  
        // ]);

       // $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $isAdmin = $user->is_admin;

     

        $imagePaths = [];
        foreach (['image', 'company_logo', 'company_logo1', 'company_logo2'] as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('images', 'public');
                $imagePaths[$field] = basename($path);
            }
        }

        $signature->update(array_merge(
            $validatedDataUser,
            $imagePaths
        ));
        
        $image = Image::updateOrCreate(
            ['user_id' => $user->id],
            $imagePaths
        );

        return response()->json([
            'message' => 'User information updated successfully',
            'user' => $user,
            'signature' => $validatedDataUser,
            'image' => $image

        ], 200);
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            $signature = Signature::find($id);
            $isAdmin = $user->is_admin;
            $currentUser = Auth::user();

            // If not an admin, ensure that the user can only delete their own information
            if (! $currentUser->isAdmin() && $currentUser->id !== $signature->user_id) {
                return response()->json(['message' => 'Unauthorized to delete this user.'], 403);
            }

            $signature = Signature::where('id', $id)->first();

            if (!$signature) {
                return response()->json(['message' => 'Signature not found.'], 404);
            }

            $signature->delete();

            // Delete the associated images from the storage
            foreach (['image', 'company_logo', 'company_logo1', 'company_logo2'] as $field) {
                if ($signature->$field) {
                    Storage::delete('public/images/' . $signature->$field);
                }
            }

            return response()->json(['message' => 'Signature deleted successfully.'], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to delete signature', 'message' => $e->getMessage()], 500);
        }
    }
}
