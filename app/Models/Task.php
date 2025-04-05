<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'goal_id', 
        'title', 
        'description',       
        'estimated_time', // ⏳ New field for estimated task duration
        'status', // 🏁 New field to track progress
        'priority', // ⚡ New field for task importance
        "is_recursive",
        "date",
        "start_time",
        "end_time",
    ];

    protected $attributes = [
        'status' => 'pending', // Default status
        'priority' => 'medium', // Default priority
        'is_recursive'=> false,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A task can belong to many timeboxes
    public function timeboxes() 
    {
        return $this->belongsToMany(Timebox::class, 'task_timebox')
                    ->withTimestamps();
    }

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }
}
