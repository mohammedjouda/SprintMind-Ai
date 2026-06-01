<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'description','status', 'is_completed', 'user_id'];

    // Define relationship: Task belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
