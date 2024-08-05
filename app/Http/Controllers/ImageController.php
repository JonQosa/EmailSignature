<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserImage;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function getImages($userId)
    {
        try {
            $user = Auth::user();
            if ($user->id != $userId && !$user->isAdmin()) { // Replace isAdmin() with your admin check method
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            $images = UserImage::where('user_id', $userId)->firstOrFail();
            return response()->json(['images' => $images]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
