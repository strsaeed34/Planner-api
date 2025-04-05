<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Goal;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
   
    // Create a new goal
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7', // Optional, hex color format
        ]);

        $goal = Goal::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description'=> $request->description,
            'color' => $request->color ?? '#3498db',
        ]);

        return response()->json($goal, 201);
    }

    public function show(Goal $goal)
    {
        if (Auth::id() !== $goal->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json($goal);
    }

    // Fetch all user goals
    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())->get();
        return response()->json($goals);
    }

    // Update a goal
    public function update(Request $request, $id)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'color' => 'sometimes|string|max:7',
        ]);

        $goal->update($request->only(['name', 'color','description']));

        return response()->json($goal);
    }

    // Delete a goal
    public function destroy(Goal $goal)
    {
        if (Auth::id() !== $goal->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $goal->delete();

        return response()->json(['message' => 'Goal deleted successfully']);
    }
}
