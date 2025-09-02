<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;


class AuthController extends Controller
{
    public function index()
    {
        // return User::all(); // return all users
         return User::select('id', 'name', 'email', 'role', 'status')->get();

    }

    public function me(Request $request)
    {
        // return $request->user(); // return current logged-in user
        return $request->user()->only(['id', 'name', 'email', 'role', 'status']);

    }
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'nullable|in:preparer,approver,admin',
        ]);


        $data['password'] = Hash::make($data['password']);
        $data['status'] = 'pending'; // default status
        $user = User::create($data);

        return response()->json([
            // 'user' => $user
            'user' => [
            'id'     => $user->id,
            'name'   => $user->name,
            'email'  => $user->email,
            'status' => $user->status,
        ]
        ], 201);
    }

public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (!Auth::attempt($credentials)) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    $user = Auth::user();

    // Check user status
    if ($user->status === 'deactivated') {
        // Log out immediately
        Auth::logout();

        return response()->json([
            'error' => 'Your account is deactivated. Contact admin.'
        ], 403);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        // 'user' => $user,
        'user' => [
        'id'     => $user->id,
        'name'   => $user->name,
        'email'  => $user->email,
        'status' => $user->status,
        'role'   => $user->role,
    ],
        'token' => $token
    ]);
}
   public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Token revoked. Logged out successfully']);
}

public function toggleStatus($id)
{
    $user = User::findOrFail($id);

    if ($user->status === 'active') {
        $user->status = 'deactivated';
    } else {
        $user->status = 'active';
    }

    $user->save();

    return response()->json(['message' => 'User status updated', 'user' => $user]);
}

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }
    public function handleGoogleCallback()
    {
        try {
        $googleUser = Socialite::driver('google')->stateless()->user();

            // First or create user
            $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                    'password' => bcrypt(str()->random(16)),
                    'role' => 'pending', // default = pending until admin approves
                ]
        );

            // Issue Sanctum token
            $token = $user->createToken('api-token')->plainTextToken;

            // Redirect to frontend with token
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

            return redirect()->away($frontendUrl . '/auth/callback?token=' . $token);
        } catch (\Exception $e) {
        return response()->json([
                'error' => 'Google login failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:preparer,approver,admin,pending',
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json(['message' => 'User updated', 'user' => $user]);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
}
