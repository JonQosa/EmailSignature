<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserAuthController extends Controller
{
    /**
     * Authenticate a user and issue a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
    ]);

    $user = User::create([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'password' => bcrypt($validatedData['password']),
    ]);


    return response()->json(['message' => 'User registered successfully'], 201);
}

public function getAllUsers()
{
    // Fetch all users
    $users = User::all();

    // Return response
    return response()->json(['users' => $users]);
}

public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        
        $user = User::where('email',$request->email)->first();

        $token = $user->createToken('Personal Access Token')->plainTextToken;
        $role = $user->role;
        $id = $user->id;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'role' => $role,
            'id' => $id
        ]);
    }

    // Authentication failed
    throw ValidationException::withMessages([
        'email' => ['The provided credentials are incorrect.'],
    ]);
}
public function logout(Request $request)
{
    $user = $request->user();

    if ($user) {
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
    }

    return response()->json(['message' => 'Logout successful']);
}

public function deleteUserById($user_id)
{
    try {
        $user = User::findOrFail($user_id);
        $user->delete();
        return response()->json(null, 204);
    } catch (\Exception $e) {
        Log::error('Error deleting user: ' . $e->getMessage());
        return response()->json(['error' => 'Internal Server Error'], 500);
    }
}
}
