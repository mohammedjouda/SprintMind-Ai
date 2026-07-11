<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        // جلب مشاريع المستخدم الحالي فقط مع أعداد السبرنتات والمهام لتجنب N+1 Problem
        $query = $request->user()->projects()
            ->with(['sprints' => function($q) {
                $q->where('status', 'active');
            }])
            ->withCount([
                'sprints', 
                'tasks', 
                'tasks as completed_tasks_count' => function ($q) {
                    $q->where('status', 'completed');
                }
            ])
            ->latest();

        // فلترة حسب التصنيف إن وجد من واجهة التبويبات (Alpine.js Tabs)
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $projects = $query->paginate(9);

        // إحصائيات علوية سريعة للشريط التنفيذي
        $totalProjects = $request->user()->projects()->count();
        $activeSprintsCount = \App\Models\Sprint::whereIn('project_id', $request->user()->projects()->pluck('id'))
            ->where('status', 'active')->count();

        return view('projects.index', compact('projects', 'totalProjects', 'activeSprintsCount'));
    }

    /**
     * حفظ مشروع جديد في قاعدة البيانات (مع التجهيز لاستدعاء الـ AI)
     */
    public function store(StoreProjectRequest $request)
    {
        try {
            // استخدام الـ Transaction لضمان سلامة العمليات المتسلسلة
            $project = DB::transaction(function () use ($request) {

                // 1. إنشاء المشروع وربطه بالمستخدم الحالي
                $newProject = $request->user()->projects()->create([
                    'name' => $request->name,
                    'category' => $request->category,
                    'expected_duration' => $request->expected_duration,
                    'description' => $request->description,
                    'use_ai_scaffold' => $request->boolean('use_ai_scaffold', false),
                    'status' => 'active',
                ]);

                // 2. فحص هل طلب المستخدم التفكيك التلقائي بالذكاء الاصطناعي؟
                if ($newProject->use_ai_scaffold) {
                    /*
                     |--------------------------------------------------------------------------
                     | AI Agile Architect Integration (TODO: Step 2)
                     |--------------------------------------------------------------------------
                     | هنا سنقوم لاحقاً باستدعاء الخدمة الذكية:
                     | app(\App\Services\AIAgileArchitectService::class)->scaffold($newProject);
                     |
                     */
                    Log::info("AI Scaffolding requested for project ID: {$newProject->id}");
                }

                return $newProject;
            });

            return redirect()->route('projects.show', $project)
                ->with('success', 'تم تأسيس المشروع ومساحة العمل بنجاح! ');
        } catch (\Exception $e) {
            Log::error('Error creating project: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'حدث خطأ غير متوقع أثناء حفظ المشروع. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * عرض مساحة العمل الفردية للمشروع (تفاصيل المشروع + السبرنتات + Backlog المهام)
     */
    public function show(Project $project)
    {
        // حماية أمنية: التأكد أن المشروع يخص المستخدم الحالي (Authorization)
        abort_if($project->user_id !== auth()->id(), 403, 'غير مصرح لك بدخول هذه المساحة.');

        // جلب السبرنتات مع مهامها، وجلب المهام العامة (التي بدون سبرنت Backlog)
        $project->load([
            'sprints' => function ($query) {
                $query->withCount('tasks')->latest();
            },
            'tasks' => function ($query) {
                // المهام التي لم تُربط بسبرنت بعد (Backlog Tasks)
                $query->whereNull('sprint_id')->latest();
            }
        ]);

        // حساب إحصائيات الإنجاز الخاصة بهذا المشروع تحديداً
        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->where('status', 'completed')->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        $totalStoryPoints = $project->tasks()->sum('story_points');

        return view('projects.show', compact(
            'project',
            'totalTasks',
            'completedTasks',
            'completionRate',
            'totalStoryPoints'
        ));
    }

    /**
     * عرض صفحة تعديل المشروع
     */
    public function edit(Project $project)
    {
        abort_if($project->user_id !== auth()->id(), 403);
        return view('projects.edit', compact('project'));
    }

    /**
     * تحديث بيانات المشروع
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        abort_if($project->user_id !== auth()->id(), 403);

        $project->update($request->validated());

        return redirect()->route('projects.show', $project)
            ->with('success', 'تم تحديث بيانات المشروع بنجاح.');
    }

    /**
     * حذف المشروع (سيقوم تلقائياً بحذف السبرنتات والمهام المرتبطة به بناءً على Cascade الداتا بيز)
     */
    public function destroy(Project $project)
    {
        abort_if($project->user_id !== auth()->id(), 403);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'تم حذف المشروع وجميع ملحقاته بنجاح.');
    }
}
