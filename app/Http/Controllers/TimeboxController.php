<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Timebox;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
	use Carbon\Carbon;


class TimeboxController extends Controller
{

    public function index()
    {
        $timeboxes = Auth::user()->timeboxes()->get();
        return response()->json($timeboxes);
    }
    /* add todays timebox
    public function getOrCreateTodayTimebox()
    {
        $userId = Auth::id();
      $today = now()->toDateString();
        
        // Check if today's timebox already exists
        $timebox = Timebox::where('user_id', $userId)
                          ->where('date', $today)
                          ->first();
    
        if (!$timebox) {
            // Create new timebox for today
            $timebox = Timebox::create([
                'user_id' => $userId,
                'date' => $today,
                'start_time' => '08:00:00', // Default start time
                'end_time' => '22:00:00',   // Default end time
            ]);
        }
    
        return response()->json(['timebox' => $timebox]);
    }
	*/
	



public function getOrCreateTodayTimebox(Request $request)
{
    try {
        $userId = Auth::id();

        // Get the date from the request
        $date = $request->input('date');

        // If no date is provided, default to today's date
        if (!$date) {
            $date = now()->toDateString();
        }

        // Parse the date to ensure it's in the correct format (YYYY-MM-DD)
        $date = Carbon::parse($date)->toDateString();

        // Check if a timebox already exists for the given date
        $timebox = Timebox::where('user_id', $userId)
                          ->where('date', $date)
                          ->first();

        if ($timebox) {
            return response()->json(
               $timebox
            );
        }

        // Create a new timebox for the given date
        $timebox = Timebox::create([
            'user_id' => $userId,
            'date' => $date,
            'start_time' => '08:00:00', // Default start time
            'end_time' => '22:00:00',   // Default end time
        ]);

        return response()->json(
           $timebox,
        );
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to get or create timebox: ' . $e->getMessage(),
        ], 500);
    }
}

    // ✅ Assign Task to Timebox
   public function assignTask(Request $request, Timebox $timebox)
{
    $request->validate([
        'task_id' => 'required|exists:tasks,id',
    ]);

    $task = Task::findOrFail($request->task_id);

    // Ensure the task belongs to the authenticated user
    if ($task->user_id !== Auth::id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Check if the task is already in the timebox (prevent redundancy)
    if ($timebox->tasks()->where('task_id', $task->id)->exists()) {
        return response()->json(['error' => 'Task already assigned to this timebox'], 400);
    }

    // Get all tasks in the timebox
    $existingTasks = $timebox->tasks;

    // Convert task's start_time to hours only
    $newTaskHour = $task->start_time ? date('H', strtotime($task->start_time)) : null;

    // Check for overlap (if any task in the timebox shares the same hour)
    foreach ($existingTasks as $existingTask) {
        $existingTaskHour = $existingTask->start_time ? date('H', strtotime($existingTask->start_time)) : null;

        if ($newTaskHour !== null && $existingTaskHour !== null && $newTaskHour == $existingTaskHour) {
            return response()->json(['error' => 'Time conflict! Another task exists in this hour.'], 400);
        }
    }

    // Attach task to timebox (Many-to-Many)
    $timebox->tasks()->attach($task->id);

    // Update task's date to match timebox
    $task->update([
        'date' => $timebox->date, // Update the task's date
    ]);

    return response()->json(['message' => 'Task assigned to timebox successfully']);
}



    // ✅ Fetch Timebox with Tasks
    public function show(Timebox $timebox)
    {
        // Ensure the timebox belongs to the authenticated user
        if ($timebox->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'timebox' => $timebox->load('tasks'), // Load the timebox with its tasks
        ]);
    }


    // ✅ Remove Task from Timebox
    public function removeTask(Timebox $timebox, Task $task)
    {
        // Ensure both the timebox and task belong to the authenticated user
        if ($timebox->user_id !== Auth::id() || $task->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Detach the task from the timebox
        $timebox->tasks()->detach($task->id);

        return response()->json(['message' => 'Task removed from timebox successfully']);
    }


  
}


?>