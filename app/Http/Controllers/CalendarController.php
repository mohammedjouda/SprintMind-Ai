<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\AIAgileArchitectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    /**
     * Display the calendar view page.
     */
    public function index()
    {
        // Programmatically run migrations if start_date column doesn't exist yet
        if (!\Illuminate\Support\Facades\Schema::hasColumn('tasks', 'start_date')) {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            } catch (\Exception $e) {
                Log::error('Auto-migration failed: ' . $e->getMessage());
            }
        }

        return view('calendar');
    }

    /**
     * Fetch scheduled tasks for FullCalendar.
     */
    public function events(Request $request)
    {
        $tasks = $request->user()->tasks()
            ->with('project')
            ->whereNotNull('due_date')
            ->get();

        $events = $tasks->map(function ($task) {
            $start = $task->start_date ? $task->start_date->format('Y-m-d') : ($task->due_date ? $task->due_date->format('Y-m-d') : null);
            $end = $task->due_date ? $task->due_date->format('Y-m-d') : null;

            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $start,
                'end' => $end,
                'allDay' => true,
                'extendedProps' => [
                    'priority' => $task->priority ?? 'medium',
                    'story_points' => $task->story_points ?? 0,
                    'status' => $task->status ?? 'pending',
                    'is_ai_generated' => (bool) $task->is_ai_generated,
                    'project_name' => $task->project?->name ?? 'بدون مشروع',
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Fetch unscheduled tasks for the sidebar backlog.
     */
    public function unscheduled(Request $request)
    {
        $tasks = $request->user()->tasks()
            ->with('project')
            ->whereNull('due_date')
            ->where('status', '!=', 'completed')
            ->get();

        return response()->json($tasks);
    }

    /**
     * Reschedule a task via Drag-and-Drop or resizing on the calendar.
     */
    public function reschedule(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['error' => 'غير مصرح لك بالتعديل على هذه المهمة'], 403);
        }

        $validated = $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'due_date' => 'nullable|date_format:Y-m-d',
        ]);

        try {
            DB::transaction(function () use ($task, $validated) {
                $task->update([
                    'start_date' => $validated['start_date'] ?? null,
                    'due_date' => $validated['due_date'] ?? null,
                ]);
            });

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Rescheduling task ID ' . $task->id . ' failed: ' . $e->getMessage());
            return response()->json(['error' => 'فشلت عملية إعادة الجدولة'], 500);
        }
    }

    /**
     * Trigger the Gemini Auto-Scheduler.
     */
    public function autoSchedule(Request $request, AIAgileArchitectService $aiService)
    {
        $unscheduled = $request->user()->tasks()
            ->whereNull('due_date')
            ->where('status', '!=', 'completed')
            ->get();

        if ($unscheduled->isEmpty()) {
            return response()->json([
                'success' => true,
                'count' => 0,
                'message' => 'لا توجد مهام غير مجدولة في الـ Backlog حالياً.',
            ]);
        }

        $startDate = $request->input('start_date', now()->format('Y-m-d'));

        try {
            $scheduledData = $aiService->autoScheduleTasks($unscheduled, $startDate);

            $updatedCount = 0;

            DB::transaction(function () use ($scheduledData, $request, &$updatedCount) {
                foreach ($scheduledData as $item) {
                    $taskId = $item['task_id'] ?? null;
                    $start = $item['start_date'] ?? null;
                    $due = $item['due_date'] ?? null;

                    if ($taskId && $start && $due) {
                        $task = $request->user()->tasks()->find($taskId);
                        if ($task) {
                            $task->update([
                                'start_date' => $start,
                                'due_date' => $due,
                            ]);
                            $updatedCount++;
                        }
                    }
                }
            });

            return response()->json([
                'success' => true,
                'count' => $updatedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Auto-Scheduling failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'فشلت جدولة الذكاء الاصطناعي: ' . $e->getMessage(),
            ], 500);
        }
    }
}
