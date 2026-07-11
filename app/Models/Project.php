<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Sprint;
use App\Models\Task;

class Project extends Model
{
    protected $fillable = ['user_id', 'name', 'is_inbox', 'category', 'expected_duration', 'description', 'use_ai_scaffold', 'status'];

    protected $casts = [
        'use_ai_scaffold' => 'boolean',
        'is_inbox' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sprints()
    {
        return $this->hasMany(Sprint::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
