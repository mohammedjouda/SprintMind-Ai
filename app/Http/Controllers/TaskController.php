<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Http\Requests\StoreTaskRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * عرض قائمة المهام العامة للمستخدم (Ops Center / Backlog)
     */
    public function index(Request $request)
    {
        // جلب مهام المستخدم الحالي فقط مع أسم المشاريع والسبرنتات (Eager Loading)
        $query = $request->user()->tasks()
            ->with(['project', 'sprint'])
            ->latest();

        // تصفية المهام حسب الأولوية أو الحالة إن تم تحديدها في الرابط
        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->paginate(10);

        // --- إحصائيات سريعة للوحة القيادة التشغيلية ---
        $totalTasks = $request->user()->tasks()->count();
        $completedTasks = $request->user()->tasks()->where('status', 'completed')->count();
        $pendingTasks = $totalTasks - $completedTasks;
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // إحصائيات الأولوية القصوى (High Priority)
        $highTasksTotal = $request->user()->tasks()->where('priority', 'high')->count();
        $highTasksDone = $request->user()->tasks()->where('priority', 'high')->where('status', 'completed')->count();
        $highTasksPending = $highTasksTotal - $highTasksDone;
        $highCompletionRate = $highTasksTotal > 0 ? round(($highTasksDone / $highTasksTotal) * 100) : 0;

        // إحصائيات الذكاء الاصطناعي (AI Velocity)
        $aiTasksTotal = $request->user()->tasks()->where('is_ai_generated', true)->count();
        $storyPointsBurned = $request->user()->tasks()->where('status', 'completed')->sum('story_points');

        // إحصائيات الـ Backlog
        $backlogTasksTotal = $request->user()->tasks()->whereNull('sprint_id')->count();

        return view('tasks.index', compact(
            'tasks',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'completionRate',
            'highTasksTotal',
            'highTasksDone',
            'highTasksPending',
            'highCompletionRate',
            'aiTasksTotal',
            'storyPointsBurned',
            'backlogTasksTotal'
        ));
    }

    /**
     * عرض صفحة إضافة مهمة جديدة يدوياً
     */
    public function create(Request $request)
    {
        // جلب مشاريع المستخدم لاختيار المشروع التابعة له المهمة
        $projects = $request->user()->projects()->select('id', 'name')->latest()->get();

        // جلب كل سبرنتات مشاريع المستخدم لفلترتها في الواجهة
        $sprints = \App\Models\Sprint::whereIn('project_id', $projects->pluck('id'))->get();

        // إذا كان هناك project_id ممرر في الرابط (مثلاً أضاف المهمة من داخل مشروع معين)
        $selectedProjectId = $request->query('project_id');

        return view('tasks.create', compact('projects', 'sprints', 'selectedProjectId'));
    }

    /**
     * حفظ المهمة الجديدة في قاعدة البيانات مع معايير القبول
     */
    public function store(StoreTaskRequest $request)
    {
        $projectId = $request->project_id;

        if (is_null($projectId)) {
            $inboxProject = auth()->user()->projects()->firstOrCreate(
                ['is_inbox' => true],
                [
                    'name' => 'Inbox',
                    'category' => 'personal',
                    'status' => 'active',
                ]
            );
            $projectId = $inboxProject->id;
        }

        // التحقق من أن المشروع المحدد ينتمي فعلياً للمستخدم الحالي
        $project = Project::findOrFail($projectId);
        abort_if($project->user_id !== auth()->id(), 403, 'غير مصرح لك بإضافة مهام لهذا المشروع.');

        try {
            $task = DB::transaction(function () use ($request, $project) {
                // 1. إنشاء المهمة
                $newTask = $project->tasks()->create([
                    'user_id' => auth()->id(),
                    'sprint_id' => $request->sprint_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'priority' => $request->priority,
                    'status' => $request->status ?? 'pending',
                    'story_points' => $request->story_points ?? 0,
                    'due_date' => $request->due_date,
                    'is_ai_generated' => false, // المهمة اليدوية تأخذ false دائماً
                ]);

                // 2. إذا أدخل المستخدم نقاط تحقق فرعية (Acceptance Criteria)، نقوم بحفظها
                if ($request->filled('acceptance_criteria')) {
                    foreach ($request->acceptance_criteria as $criterion) {
                        if (!empty(trim($criterion))) {
                            $newTask->acceptanceCriteria()->create([
                                'title' => trim($criterion),
                                'is_completed' => false,
                            ]);
                        }
                    }
                }

                return $newTask;
            });

            // إعادة التوجيه إلى صفحة المشروع أو صفحة المهام العامة بناءً على من أين أتى
            if ($request->has('redirect_to_project')) {
                return redirect()->route('projects.show', $project)
                    ->with('success', 'تم إضافة المهمة إلى المشروع بنجاح!');
            }

            return redirect()->route('tasks.index')
                ->with('success', 'تم إنشاء المهمة التشغيلية بنجاح!');
        } catch (\Exception $e) {
            Log::error('Error creating task: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء حفظ المهمة. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * عرض تفاصيل المهمة
     */
    public function show(Task $task)
    {
        abort_if($task->user_id !== auth()->id(), 403);

        $task->load(['project', 'sprint', 'acceptanceCriteria']);

        return view('tasks.show', compact('task'));
    }

    /**
     * عرض صفحة تعديل المهمة
     */
    public function edit(Task $task)
    {
        // حماية أمنية: التأكد أن المهمة تخص المستخدم الحالي
        abort_if($task->user_id !== auth()->id(), 403);

        $projects = auth()->user()->projects()->select('id', 'name')->get();
        // جلب سبرنتات المشروع الحالي لاختيارها في التعديل
        $sprints = \App\Models\Sprint::where('project_id', $task->project_id)->get();

        $task->load('acceptanceCriteria');

        return view('tasks.edit', compact('task', 'projects', 'sprints'));
    }

    /**
     * تحديث بيانات المهمة
     */
    public function update(StoreTaskRequest $request, Task $task)
    {
        abort_if($task->user_id !== auth()->id(), 403);

        $data = $request->validated();

        if (empty($data['project_id'])) {
            $inboxProject = auth()->user()->projects()->firstOrCreate(
                ['is_inbox' => true],
                [
                    'name' => 'Inbox 📥',
                    'category' => 'personal',
                    'status' => 'active',
                ]
            );
            $data['project_id'] = $inboxProject->id;
        } else {
            $project = Project::findOrFail($data['project_id']);
            abort_if($project->user_id !== auth()->id(), 403, 'غير مصرح لك بنقل هذه المهمة لهذا المشروع.');
        }

        try {
            DB::transaction(function () use ($data, $task) {
                // 1. تحديث بيانات المهمة الأساسية
                $task->update($data);

                // 2. تحديث معايير القبول (حذف القديم وإعادة الإدخال)
                $task->acceptanceCriteria()->delete();

                if (isset($data['acceptance_criteria']) && is_array($data['acceptance_criteria'])) {
                    foreach ($data['acceptance_criteria'] as $criterion) {
                        if (!empty(trim($criterion))) {
                            $task->acceptanceCriteria()->create([
                                'title' => trim($criterion),
                                'is_completed' => false,
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('projects.show', $task->project_id)
                ->with('success', 'تم تحديث المهمة بنجاح.');
        } catch (\Exception $e) {
            Log::error('Error updating task: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث المهمة. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * دالة Action سريعة: تبديل حالة المهمة بين مكتملة وغير مكتملة (عند الضغط على Checkbox)
     */
    public function toggle(Task $task)
    {
        abort_if($task->user_id !== auth()->id(), 403);

        // تبديل الحالة
        $newStatus = $task->status === 'completed' ? 'pending' : 'completed';
        $task->update(['status' => $newStatus]);

        // إذا كان الطلب قادماً عبر AJAX أو HTMX، نرجع رداً سريعاً بـ JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => 'تم تغيير حالة المهمة'
            ]);
        }

        return back()->with('success', 'تم تحديث حالة المهمة بنجاح.');
    }

    /**
     * حذف المهمة من قاعدة البيانات
     */
    public function destroy(Task $task)
    {
        abort_if($task->user_id !== auth()->id(), 403);

        $task->delete();

        return back()->with('success', 'تم حذف المهمة من الـ Backlog.');
    }
}
