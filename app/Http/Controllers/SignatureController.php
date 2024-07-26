<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Signature;

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
        $validationDataImage = $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo1' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo2' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'gif' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Create or update the user's signature
        $signature = Signature::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $validatedDataUser['name'],
                'last_name' => $validatedDataUser['last_name'],
                'email' => $validatedDataUser['email'],
                'title' => $validatedDataUser['title'],
                'company' => $validatedDataUser['company'],
                'meeting_link' => $validatedDataUser['meeting_link'] ?? '',
                'address' => $validatedDataUser['address'],
                'website' => $validatedDataUser['website'],
                'linkedin_profile' => $validatedDataUser['linkedin_profile'],
                'company_linkedin' => $validatedDataUser['company_linkedin'] ?? '',
                'facebook' => $validatedDataUser['facebook'],
                'feedback' => $validatedDataUser['feedback'] ?? '',
                'twitter' => $validatedDataUser['twitter'] ?? '',
                'instagram' => $validatedDataUser['instagram'],
                'phone' => $validatedDataUser['phone'],
                'description' => $validatedDataUser['description'],
            ]
        );

        // Handle image uploads
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $signature->image = basename($imagePath);
        }

        if ($request->hasFile('company_logo')) {
            $companyLogoPath = $request->file('company_logo')->store('images', 'public');
            $signature->company_logo = basename($companyLogoPath);
        }

        if ($request->hasFile('company_logo1')) {
            $companyLogo1Path = $request->file('company_logo1')->store('images', 'public');
            $signature->company_logo1 = basename($companyLogo1Path);
        }

        if ($request->hasFile('company_logo2')) {
            $companyLogo2Path = $request->file('company_logo2')->store('images', 'public');
            $signature->company_logo2 = basename($companyLogo2Path);
        }

        if ($request->hasFile('gif')) {
            $gifPath = $request->file('gif')->store('images', 'public');
            $signature->gif = basename($gifPath);
        }

        // Save signature record
        $signature->save();

        return response()->json([
            'message' => 'User information and signature saved successfully',
            'user' => $user,
            'signature' => $signature
        ], 200);
    }
    public function update(Request $request, $id)
{
    try {
        // Ensure the user is authenticated
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Find the signature by ID
        $signature = Signature::where('user_id', $user->id)->find($id);

        if (!$signature) {
            return response()->json(['message' => 'You can only update your own information.'], 403);
        }

        // Validate user data
        $validatedDataUser = $request->validate([
            'name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'linkedin_profile' => 'nullable|url',
            'company_linkedin' => 'nullable|url|max:255',
            'facebook' => 'nullable|url',
            'feedback' => 'nullable|string|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'description' => 'nullable|string',
        ]);

        // Validate image data
        $validationDataImage = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_logo2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gif' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update the signature record
        $signature->update([
            'name' => $validatedDataUser['name'] ?? $signature->name,
            'last_name' => $validatedDataUser['last_name'] ?? $signature->last_name,
            'email' => $validatedDataUser['email'] ?? $signature->email,
            'title' => $validatedDataUser['title'] ?? $signature->title,
            'company' => $validatedDataUser['company'] ?? $signature->company,
            'meeting_link' => $validatedDataUser['meeting_link'] ?? $signature->meeting_link,
            'address' => $validatedDataUser['address'] ?? $signature->address,
            'website' => $validatedDataUser['website'] ?? $signature->website,
            'linkedin_profile' => $validatedDataUser['linkedin_profile'] ?? $signature->linkedin_profile,
            'company_linkedin' => $validatedDataUser['company_linkedin'] ?? $signature->company_linkedin,
            'facebook' => $validatedDataUser['facebook'] ?? $signature->facebook,
            'feedback' => $validatedDataUser['feedback'] ?? $signature->feedback,
            'twitter' => $validatedDataUser['twitter'] ?? $signature->twitter,
            'instagram' => $validatedDataUser['instagram'] ?? $signature->instagram,
            'phone' => $validatedDataUser['phone'] ?? $signature->phone,
            'description' => $validatedDataUser['description'] ?? $signature->description,
        ]);

        // Handle image uploads
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $signature->image = basename($imagePath);
        }

        if ($request->hasFile('company_logo')) {
            $companyLogoPath = $request->file('company_logo')->store('images', 'public');
            $signature->company_logo = basename($companyLogoPath);
        }

        if ($request->hasFile('company_logo1')) {
            $companyLogo1Path = $request->file('company_logo1')->store('images', 'public');
            $signature->company_logo1 = basename($companyLogo1Path);
        }

        if ($request->hasFile('company_logo2')) {
            $companyLogo2Path = $request->file('company_logo2')->store('images', 'public');
            $signature->company_logo2 = basename($companyLogo2Path);
        }

        if ($request->hasFile('gif')) {
            $gifPath = $request->file('gif')->store('images', 'public');
            $signature->gif = basename($gifPath);
        }

        // Save the updated signature record
        $signature->save();

        return response()->json([
            'message' => 'Signature updated successfully',
            'signature' => $signature
        ], 200);

    // } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //     return response()->json(['error' => 'Signature not found'], 404);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to update signature', 'message' => $e->getMessage()], 500);
    }
}

    
public function destroy($id)
{
    $user = Auth::user();

    // Find the signature by ID
    $signature = Signature::where('user_id', $user->id)->find($id);

    if (!$signature) {
        return response()->json(['message' => 'Signature not found'], 404);
    }

    // Delete the signature record
    $signature->delete();

    return response()->json(['message' => 'Signature deleted successfully'], 200);
}

}
