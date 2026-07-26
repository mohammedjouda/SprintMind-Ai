<?php

namespace App\Http\Controllers;

use App\Models\Sprint;
use App\Models\Task;
use App\Models\Project;
use App\Services\AIAgileArchitectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SprintController extends Controller
{
    protected $aiService;

    public function __construct(AIAgileArchitectService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Display all sprints.
     */
    public function index()
    {
        $user = auth()->user();

        // Get sprints grouped/sorted: active first, then planned, then completed
        $sprints = $user->sprints()
            ->orderByRaw("CASE WHEN status = 'active' THEN 1 WHEN status = 'planned' THEN 2 ELSE 3 END")
            ->orderBy('start_date', 'asc')
            ->get();

        // Also get user's projects to support standard manual creation form
        $projects = $user->projects;

        return view('sprints.index', compact('sprints', 'projects'));
    }

    /**
     * Show the Sprint Kanban Workspace.
     */
    public function show(Sprint $sprint)
    {
        abort_if($sprint->user_id !== auth()->id(), 403);

        // Load tasks and group them by status
        $sprint->load('tasks');

        return view('sprints.show', compact('sprint'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'name' => 'required|string|max:255',
            'goal' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'target_velocity' => 'nullable|integer|min:1',
        ]);

        Sprint::create([
            'user_id' => auth()->id(),
            'project_id' => $request->project_id,
            'name' => $request->name,
            'goal' => $request->goal,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'target_velocity' => $request->target_velocity ?? 40,
            'status' => 'planned',
        ]);

        return redirect()->route('sprints.index')->with('success', 'تم تجهيز سبرنتك الجديد بنجاح! جاهز للانطلاق؟ 🚀');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sprint $sprint)
    {
        abort_if($sprint->user_id !== auth()->id(), 403);
        $projects = auth()->user()->projects;
        return view('sprints.edit', compact('sprint', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sprint $sprint)
    {
        abort_if($sprint->user_id !== auth()->id(), 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'goal' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planned,active,completed',
            'target_velocity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $sprint) {
            if ($request->status === 'active') {
                auth()->user()->sprints()
                    ->where('status', 'active')
                    ->where('id', '!=', $sprint->id)
                    ->update(['status' => 'completed']);
            }

            $sprint->update([
                'name' => $request->name,
                'goal' => $request->goal,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'target_velocity' => $request->target_velocity,
            ]);
        });

        return redirect()->route('sprints.index')->with('success', 'تم حفظ التعديلات وتحديث السبرنت بنجاح! 🛠️');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sprint $sprint)
    {
        abort_if($sprint->user_id !== auth()->id(), 403);

        $sprint->delete();

        return redirect()->route('sprints.index')->with('success', 'تم حذف السبرنت وإزالة المهام التابعة له بنجاح.');
    }

    /**
     * Generate an AI-suggested sprint structure.
     */
    public function aiPlan(Request $request)
    {
        $request->validate([
            'target_velocity' => 'required|integer|min:1',
            'duration_weeks' => 'required|integer|min:1',
        ]);

        // Get unscheduled backlog tasks: user's tasks, where due_date is null AND sprint_id is null
        $unscheduledTasks = auth()->user()->tasks()
            ->whereNull('sprint_id')
            ->whereNull('due_date')
            ->get();

        if ($unscheduledTasks->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'يبدو أن قائمة المهام (Backlog) فارغة حالياً! أضف بعض المهام أولاً لتتمكن من تخطيط السبرنت بالذكاء الاصطناعي. ',
            ], 422);
        }

        try {
            $plan = $this->aiService->planSprint(
                $unscheduledTasks,
                (int) $request->target_velocity,
                (int) $request->duration_weeks
            );

            // Fetch the details of the selected tasks to display in the preview
            $selectedTasks = Task::whereIn('id', $plan['selected_task_ids'] ?? [])->get();

            return response()->json([
                'success' => true,
                'sprint_name' => $plan['sprint_name'] ?? 'سبرنت ذكي جديد',
                'sprint_goal' => $plan['sprint_goal'] ?? '',
                'selected_tasks' => $selectedTasks,
                'total_points' => $plan['total_points'] ?? 0,
                'reasoning' => $plan['reasoning'] ?? '',
                'duration_weeks' => $request->duration_weeks,
                'target_velocity' => $request->target_velocity,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Sprint Planning error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تخطيط السبرنت الذكي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Commit the AI suggested sprint to the database.
     */
    public function commitAiSprint(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'goal' => 'nullable|string',
            'duration_weeks' => 'required|integer|min:1',
            'target_velocity' => 'required|integer|min:1',
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:tasks,id',
        ]);

        try {
            $sprint = null;
            DB::transaction(function () use ($request, &$sprint) {
                // Auto-transition any currently active sprints to completed
                auth()->user()->sprints()
                    ->where('status', 'active')
                    ->update(['status' => 'completed']);

                $startDate = now();
                $endDate = now()->addWeeks((int) $request->duration_weeks);

                $sprint = Sprint::create([
                    'user_id' => auth()->id(),
                    'name' => $request->name,
                    'goal' => $request->goal,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => 'active', // Make the committed AI sprint active immediately
                    'target_velocity' => $request->target_velocity,
                ]);

                // Update the tasks with the new sprint_id and make sure status is 'todo' if it was pending
                Task::whereIn('id', $request->task_ids)
                    ->update([
                        'sprint_id' => $sprint->id,
                        'status' => 'todo',
                    ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'رائع! تم إطلاق سبرنتك الذكي بنجاح. جاري توجيهك إلى لوحة العمل الآن... ⚡',
                'redirect_url' => route('sprints.show', $sprint->id),
            ]);
        } catch (\Exception $e) {
            Log::error('AI Sprint Commit error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'فشل إنشاء السبرنت الذكي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update task status from Kanban board drag & drop.
     */
    public function updateTaskStatus(Request $request, Task $task)
    {
        abort_if($task->user_id !== auth()->id(), 403);

        $request->validate([
            'status' => 'required|in:todo,in_progress,review,completed',
        ]);

        $task->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم نقل المهمة وتحديث حالتها بنجاح! 🎉',
            'status' => $task->status,
        ]);
    }

    /**
     * Retrieve AI sprint health analysis.
     */
    public function healthCheck(Sprint $sprint)
    {
        abort_if($sprint->user_id !== auth()->id(), 403);

        try {
            $analysis = $this->aiService->analyzeSprintHealth($sprint);

            return response()->json([
                'success' => true,
                'health_status' => $analysis['health_status'] ?? 'healthy',
                'bottleneck_detected' => $analysis['bottleneck_detected'] ?? false,
                'copilot_advice' => $analysis['copilot_advice'] ?? '',
            ]);
        } catch (\Exception $e) {
            Log::error('AI Sprint Health check error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'فشل تحليل صحة السبرنت الذكي.',
            ], 500);
        }
    }
}
