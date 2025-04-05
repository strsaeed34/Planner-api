<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function show(Request $request) {

        return $request->user();
    }
    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid Credentials',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }


    public function update(Request $request)
    {
        $user = $request->user();

        // Validate the input
        $request->validate([  
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|unique:users,email,' . $user->id,         
            'password' => 'sometimes|string|min:8|confirmed',
            ]);


        // Update the user attributes
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }
       
        
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Return the updated user data
        return response()->json($user);
    }

    // public function changePassword(Request $request){
    //     $user = $request->user();
    //        // Validate the input
    //        $request->validate([  
    //         'name' => 'sometimes|string|max:255',
    //         'email' => 'sometimes|string|email|unique:users,email,' . $user->id,         
    //         'password' => 'required|string|min:8|confirmed',
    //         ]);

    //     $user->save();

    //     // Return the updated user data
    //     return response()->json($user);

    // }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function destroy(Request $request)
    {
        

        return response()->json([
            'message' => 'User Deleted successfully',
        ]);
    }
}
