<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

use function PHPUnit\Framework\isNull;

class UserController extends Controller
{
    public function index($username)
    {
        try {
            $user = User::with(['services' => function ($query) {
                $query->with(['category', 'reviews', 'user']);
            }])->where('username', $username)->first();

            if ($user) {
                // return response()->json(['message' => 'hello', 'user' => $user]);
                if ($user->is_provider === 1) {
                    return response()->json(['user' => $user, 'status' => 200, 'message' => 'Succsess'], 200);
                } else return response()->json(['status' => 403, 'message' => 'Failed to accsess the profile (this isn\'t a provider profile !?)'], 403);
            } else return response()->json(['status' => 404, 'message' => 'user not found'], 404);
        } catch (Throwable $error) {
            return response()->json(['status' => 500, 'message' => $error->message], 500);
        }
    }
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
            $token = $user->createToken('access-token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'data' => [
                    'user' => $user->only(['id', 'firstName', 'lastName', 'email']),
                ]
            ], 201)->cookie('access-token', $token, 60 * 24 * 7, '/', null, true, true); // 201 for resource creation
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
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // ACCESS token (short-lived)
        $accessToken = $user->createToken('access-token', ['*'])->plainTextToken;

        // REFRESH token (secure, long-lived random string)
        $refreshToken = Str::random(64);
        $user->refresh_token = hash('sha256', $refreshToken);
        $user->save();

        return response()->json([
            'access_token' => $accessToken,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'image' => $user->image ? asset('storage/' . $user->image) : null,
                'phone1' => $user->phone1,
                'phone2' => $user->phone2,
                'address' => $user->address
            ],
        ])->cookie('refresh_token', $refreshToken, 60 * 24 * 7, '/', 'localhost', false, true, false, 'Strict');
    }
    public function refresh(Request $request)
    {
        $refreshToken = $request->cookie('refresh_token');
        if (!$refreshToken) {
            return response()->json(['message' => 'Credentials does not match'], 401);
        }

        $user = User::where('refresh_token', hash('sha256', $refreshToken))->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        // New access token
        $accessToken = $user->createToken('access-token', ['*'])->plainTextToken;

        return response()->json([
            'access_token' => $accessToken,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'image' => $user->image ? asset('storage/' . $user->image) : null,
                'phone1' => $user->phone1,
                'phone2' => $user->phone2,
                'address' => $user->address
            ]
        ]);
    }


    public function logoutUser(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $user->refresh_token = null;
        $user->save();

        return response()->json(['message' => 'Logged out'])->withoutCookie('refresh_token');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validateUser = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);
        // return response()->json(['msg' => $validateUser->fails()]);
        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateUser->errors()
            ], 422);
        }

        $validated = $validateUser->validated();

        // Update fields
        $user->fill($validated);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            // Store new image
            $path = $request->file('image')->store('profile_images', 'public');
            $user->image = $path;
        }
        // Handle profile image removal
        elseif ($request->has('rmprofileimage') && $request->rmprofileimage) {
            // Delete old image if exists
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            // Set default image path
            $user->image = '';
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'image' => $user->image ? asset('storage/' . $user->image) : null,
                'phone1' => $user->phone1,
                'phone2' => $user->phone2,
                'address' => $user->address
            ]
            // 'image_url' => $user->image ? asset('storage/' . $user->image) : null
        ]);
    }
}




// <?php

// namespace App\Http\Controllers\Api;

// use App\Models\User;
// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Validator;

// class UserController extends Controller
// {
//     /**
//      * Register a new user
//      */
//     public function createUser(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'firstName' => 'required|string|max:255',
//             'lastName'  => 'required|string|max:255',
//             'username'  => 'required|string|unique:users,username|max:255',
//             'email'     => 'required|email|unique:users,email|max:255',
//             'password'  => 'required|string|min:8',
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'status'  => false,
//                 'message' => 'Validation error',
//                 'errors'  => $validator->errors(),
//             ], 422);
//         }

//         $user = User::create([
//             'firstName' => $request->firstName,
//             'lastName'  => $request->lastName,
//             'username'  => $request->username,
//             'email'     => $request->email,
//             'password'  => Hash::make($request->password),
//         ]);

//         // Optional: Log the user in after registration
//         Auth::login($user);

//         return response()->json([
//             'status'  => true,
//             'message' => 'User registered successfully',
//             'user'    => $user->only(['id', 'firstName', 'lastName', 'username', 'email']),
//         ], 201);
//     }

//     /**
//      * Log in a user using credentials
//      */
//     public function loginUser(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'username' => 'required|string',
//             'password' => 'required|string',
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'status'  => false,
//                 'message' => 'Validation error',
//                 'errors'  => $validator->errors(),
//             ], 422);
//         }

//         if (!Auth::attempt($request->only('username', 'password'))) {
//             return response()->json([
//                 'status'  => false,
//                 'message' => 'Invalid credentials',
//             ], 401);
//         }

//         $user = Auth::user();

//         return response()->json([
//             'status'  => true,
//             'message' => 'Logged in successfully',
//             'user'    => $user->only(['id', 'firstName', 'lastName', 'username', 'email']),
//         ]);
//     }

//     /**
//      * Log out the current user (revoke session)
//      */
//     public function logoutUser(Request $request)
//     {
//         $request->session()->invalidate();
//         $request->session()->regenerateToken();

//         return response()->json([
//             'status'  => true,
//             'message' => 'Logged out successfully',
//         ]);
//     }

//     /**
//      * Return authenticated user profile
//      */
//     public function index()
//     {
//         $user = User::with('services')->findOrFail(Auth::id());

//         return response()->json([
//             'status' => true,
//             'user'   => $user,
//         ]);
//     }

//     /**
//      * Check if the user is authenticated
//      */
//     public function isAuthenticated()
//     {
//         return response()->json([
//             'authenticated' => Auth::check(),
//             'user'          => Auth::user()?->only(['id', 'username', 'email']),
//         ]);
//     }

//     /**
//      * Update user profile (if needed)
//      */
//     public function updateUser(Request $request)
//     {
//         $user = Auth::user();

//         $validator = Validator::make($request->all(), [
//             'firstName' => 'required|string|max:255',
//             'lastName'  => 'required|string|max:255',
//             'username'  => 'required|string|max:255|unique:users,username,' . $user->id,
//             'email'     => 'required|string|email|max:255|unique:users,email,' . $user->id,
//             'phone1'    => 'required|string|max:15',
//             'phone2'    => 'nullable|string|max:15',
//             'address'   => 'nullable|string|max:255',
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Validation error',
//                 'errors' => $validator->errors(),
//             ], 422);
//         }

//         $user->update($validator->validated());

//         return response()->json([
//             'status' => true,
//             'message' => 'User updated successfully',
//         ]);
//     }
// }
