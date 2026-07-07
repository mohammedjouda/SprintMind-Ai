<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. جلب المهام الأساسية للجدول (مع الترقيم)
        // يفضل جلب مهام المستخدم الحالي فقط: auth()->user()->tasks()->latest()->paginate(10);
        $tasks = auth()->user()->tasks()->latest()->paginate(10);

        // 2. حساب إحصائيات الإنجاز العام
        $totalTasks = Task::count();
        $completedTasks = Task::query()->where('status', 'completed')->count();
        $pendingTasks = $totalTasks - $completedTasks;
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // 3. إحصائيات الضرورة القصوى
        $highTasksTotal = Task::query()->where('priority', 'high')->count();
        $highTasksDone = Task::query()->where('priority', 'high')->where('status', 'completed')->count();
        $highTasksPending = $highTasksTotal - $highTasksDone;
        $highCompletionRate = $highTasksTotal > 0 ? round(($highTasksDone / $highTasksTotal) * 100) : 0;

        // 4. إحصائيات الذكاء الاصطناعي (تأكد من إضافة هذه الحقول لاحقاً في الـ Migration)
        // إذا لم تقم بإنشائها بعد في الداتا بيز، يمكنك وضع قيم وهمية مؤقتاً
        $aiTasksTotal = Task::query()->where('is_ai_generated', true)->count();

        // جمع نقاط الجهد للمهام المكتملة
        $storyPointsBurned = Task::query()->where('status', 'completed')->sum('story_points');

        // 5. إرسال كل هذه المتغيرات النظيفة إلى ملف الـ Blade
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
            'storyPointsBurned'
        ));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
        ]);
        Task::create($request->all() + ['user_id' => auth()->id()]);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $task = Task::find($id);
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task = Task::find($id);
        $task->update($request->all() + ['user_id' => auth()->id()]);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::find($id);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }

    // دالة تبديل حالة المهمة بين مكتملة وغير مكتملة
    public function toggleComplete(Task $task)
    {
        // حماية: التأكد من أن المهمة تنتمي للمستخدم الحالي لمنع التلاعب عبر الـ URL أو الـ API
        if ($task->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بتعديل هذه المهمة.');
        }

        // عكس الحالة: إذا كانت completed تصبح pending، والعكس صحيح
        $newStatus = $task->status === 'completed' ? 'pending' : 'completed';

        $task->update([
            'status' => $newStatus
        ]);

        // إعادة توجيه المستخدم لنفس الصفحة مع رسالة نجاح خفيفة
        return redirect()->back()->with('success', 'تم تحديث حالة المهمة بنجاح!');
    }
}
