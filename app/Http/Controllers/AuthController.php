<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    /**
     * @bodyParam name string required The name of the user. Example: John Doe
     * @bodyParam email string required The email of the user. Example: john@example.com
     * @bodyParam password string required The password of the user. Example: secret123
     * @bodyParam password_confirmation string required The password confirmation. Example: secret123
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @unauthenticated
     */
    public function register(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Assign default role
        $user->assignRole('user');

        // Generate a personal access token for the user
        $token = $user->createToken('authToken')->plainTextToken;

        // Return a success response with user data and token
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }
    /**
     * @unauthenticated
     */
    public function login(Request $request)
    {
        // Validate login request
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check if the password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Generate a personal access token
        $token = $user->createToken('authToken')->plainTextToken;

        // Get user roles (assuming you have assigned roles with Spatie)
        $roles = $user->getRoleNames(); // This returns a collection of roles

        // Return user, token, and roles
        return response()->json([
            'user' => $user,
            'roles' => $roles, // Return the roles as part of the response
            'token' => $token,
        ]);
    }

    /**
     * @authenticated
     */
    public function logout(Request $request)
    {
        // Revoke all tokens for the authenticated user
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
