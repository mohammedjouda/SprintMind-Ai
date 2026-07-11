<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\AIPromptController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::post('/ai-prompt/preview', [AIPromptController::class, 'preview'])->name('ai.prompt.preview');
    Route::post('/ai-prompt/save', [AIPromptController::class, 'save'])->name('ai.prompt.save');

    // السطر الجديد المعرّف للمسار [tasks.toggle]
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');


    Route::resource('projects', ProjectController::class);
    Route::post('/sprints/ai-plan', [SprintController::class, 'aiPlan'])->name('sprints.ai-plan');
    Route::post('/sprints/commit-ai-sprint', [SprintController::class, 'commitAiSprint'])->name('sprints.commit-ai-sprint');
    Route::patch('/sprints/tasks/{task}/status', [SprintController::class, 'updateTaskStatus'])->name('sprints.tasks.update-status');
    Route::get('/sprints/{sprint}/health-check', [SprintController::class, 'healthCheck'])->name('sprints.health-check');
    Route::resource('sprints', SprintController::class);
    Route::resource('tasks', TaskController::class);

    // Notes Hub Routes
    Route::patch('/notes/{note}/toggle-pin', [NoteController::class, 'togglePin'])->name('notes.toggle-pin');
    Route::post('/notes/analyze', [NoteController::class, 'analyze'])->name('notes.analyze');
    Route::post('/notes/commit-tasks', [NoteController::class, 'commitTasks'])->name('notes.commit-tasks');
    Route::resource('notes', NoteController::class);

    // Agile Calendar Routes
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
    Route::get('/calendar/unscheduled', [CalendarController::class, 'unscheduled'])->name('calendar.unscheduled');
    Route::match(['POST', 'PATCH'], '/calendar/reschedule/{task}', [CalendarController::class, 'reschedule'])->name('calendar.reschedule');
    Route::post('/calendar/auto-schedule', [CalendarController::class, 'autoSchedule'])->name('calendar.auto-schedule');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
