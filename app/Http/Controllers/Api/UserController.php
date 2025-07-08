<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'username' => 'required|string|unique:users,username|max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'password' => 'required|string|min:8'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 422); // 422 is more appropriate for validation errors
            }

            $user = User::create([
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(10),
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'data' => [
                    'user' => $user->only(['id', 'firstName', 'lastName', 'email']),
                ]
            ], 201)->cookie('token', $token, 60 * 24 * 7, '/', null, true, true); // 201 for resource creation

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 422);
            }

            if (!Auth::attempt($request->only(['username', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            $user = User::where('username', $request->username)->first();
            $token = $user->createToken('auth_token', ['*'])->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
                'token' => $token,  
                'data' => [
                    'user' => $user->only(['id', 'firstName', 'lastName', 'email']),
                ]
            ], 200)->cookie('token', $token, 60 * 24 * 7, '/', null, true, true);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Logout User
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutUser(Request $request)
    {
        // Check if user is authenticated via Sanctum
        if (!auth()->guard('sanctum')->check()) {
            return response()->json([
                'status' => false,
                'message' => 'No authenticated user',
            ], 401);
        }

        // Revoke all tokens (or just current if you prefer)
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ])->withoutCookie('token');
    }
    public function updateUser(Request $request)
    {
        $user = User::findOrFail($request->id);
        return view('welcome');
        // $validated = $request->validate([
        //     'firstName' => 'required|string|max:255',
        //     'lastName' => 'required|string|max:255',
        //     'username' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        //     'phone1' => 'required|string|max:15',
        //     'phone2' => 'nullable|string|max:15',
        //     'address' => 'nullable|string|max:255',
        // ]);
        $validateUser = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,',
            'phone1' => 'required|string|max:15',
            'phone2' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
        ]);

        $user->firstName = $validateUser['firstName'];
        $user->lastName = $validateUser['lastName'];
        $user->username = $validateUser['username'];
        $user->email = $validateUser['email'];
        $user->phone1 = $validateUser['phone1'];

        if (isset($validateUser['phone2'])) {
            $user->phone2 = $validateUser['phone2'];
        }

        if (isset($validateUser['address'])) {
            $user->address = $validateUser['address'];
        }

        $user->save();

        return response()->json(['message' => 'User updated successfully']);
    }
}
