<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'sprint_id',
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'story_points',
        'due_date',
        'start_date',
        'is_ai_generated'
    ];

    // تحويل أنواع البيانات
    protected $casts = [
        'due_date' => 'date',
        'start_date' => 'date',
        'is_ai_generated' => 'boolean',
    ];

    // --- العلاقات (Relationships) ---

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function sprint()
    {
        return $this->belongsTo(Sprint::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function acceptanceCriteria()
    {
        return $this->hasMany(AcceptanceCriteria::class);
    }
}
