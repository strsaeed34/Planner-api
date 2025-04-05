<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
       

        return response()->json([
            'message' => 'welcome to your dashboard',
           
        ]);
    }

    public function listUsers()
    {
        $users = User::all();
        return response()->json($users);
    }


    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    public function updateUserRole(Request $request, $id)
    {
        $request->validate([
            'role_id' => 'required|string|in:1,2',
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->role_id = $request->role_id;
        $user->save();

        return response()->json(['message' => 'User role updated successfully', 'user' => $user], 200);
    }
}
