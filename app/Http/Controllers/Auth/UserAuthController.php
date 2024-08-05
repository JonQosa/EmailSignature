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
public function destroyByUserId(){
    try {
        $signatures = Signature::where('user_id', $user_id)->get();
        if ($signatures->isEmpty()) {
            \Log::info('No signatures found for user_id:', ['user_id' => $user_id]);
            return response()->json(['message' => 'No signatures found for the user'], 404);
        }
        foreach ($signatures as $signature) {
            $signature->delete();
        }
        \Log::info('Signatures deleted for user_id:', ['user_id' => $user_id]);
        return response()->json(['message' => 'Signatures deleted successfully'], 200);
    } catch (\Exception $e) {
        \Log::error('Failed to delete signatures for user_id:', ['user_id' => $user_id, 'error' => $e->getMessage()]);
        return response()->json(['message' => 'Failed to delete signatures', 'error' => $e->getMessage()], 500);
    }
}
}
