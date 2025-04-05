<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Timebox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Auth::user()->tasks()->orderBy('priority', 'desc')->get();
        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'goal_id' => 'sometimes|nullable|integer|exists:goals,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_recursive' => 'nullable|boolean',
            'estimated_time' => 'nullable|integer',
            'status' => 'in:pending,in_progress,completed,canceled',
            'priority' => 'in:low,medium,high',
            'date'=> 'nullable',
            'start_time' => 'nullable|date_format:H:i', // Validate time format (HH:MM)
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time', // Validate time format and ensure it's after start_time
        ]);
        $task = Auth::user()->tasks()->create($validated);
        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        if (Auth::id() !== $task->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        if (Auth::id() !== $task->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validated = $request->validate([
            'goal_id' => 'sometimes|nullable|integer|exists:goals,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'estimated_time' => 'nullable|integer',
            'is_recursive' => 'nullable|boolean',
            'status' => 'in:pending,in_progress,completed,canceled',
            'priority' => 'in:low,medium,high',
            'date'=> 'nullable',
            'start_time' => 'nullable', // Validate time format (HH:MM)
            'end_time' => 'nullable|after_or_equal:start_time', // Validate time format and ensure it's after start_time
        ]);

        $task->update($validated);
        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if (Auth::id() !== $task->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }


    public function addToTimebox(Request $request, Task $task, Timebox $timebox)
    {
        // Ensure the authenticated user owns both the task and timebox
        if (Auth::id() !== $task->user_id || Auth::id() !== $timebox->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Attach task to timebox (task_timebox table)
        $task->timeboxes()->attach($timebox->id);

        // Update task's start_time & date based on timebox
        $task->update([
            'start_time' => $timebox->start_time,
            'date' => $timebox->date,
        ]);

        return response()->json(['message' => 'Task added to timebox successfully', 'task' => $task]);
    }
}
