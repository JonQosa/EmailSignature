<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AdminAuthController extends Controller
{    public function changeRole(Request $request, $userId)
    {
        // Validate request inputs
        $request->validate([
            'role' => 'required|in:admin,user', // Ensure the role is either 'admin' or 'user'
        ]);

        // Find the user by ID
        $user = User::findOrFail($userId);

        // Update the user's role
        $user->role = $request->input('role');
        $user->save();

        // Optionally, you can return a response indicating success
        return response()->json(['message' => 'User role updated successfully', 'user' => $user]);
    }


}
