<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timebox extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id','start_time','end_time','date',
    ];


    // A timebox belongs to a user
    public function user (){
        return $this->belongsTo(User::class);
    }

    // A timebox can have many tasks
    public function tasks(){
        return $this->belongsTo(Task::class, 'task_timebox')
        ->withTimestamps();
    }

}
