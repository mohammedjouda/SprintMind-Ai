<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // 1. جلب المهام ذات (الضرورة القصوى) التابعة للمستخدم المسجل دخول فقط
        $tasks = $request->user()->tasks()
            ->with(['project', 'sprint']) // Eager Loading لتسريع الاستعلام ومنع الـ N+1
            ->where('priority', 'high')   // تصفية المهام ذات الأولوية القصوى فقط
            ->where('status', '!=', 'completed') // (مستحسن) إخفاء المكتملة للتركيز على ما يتطلب إنجازاً
            ->latest()
            ->paginate(10);

        // 2. إحصائيات سريعة لتعمل الرسوم البيانية (توزيع الحالة) في صفحة اللوحة بدقة
        $userTasksQuery = $request->user()->tasks();

        $pendingCount = (clone $userTasksQuery)->where('status', 'pending')->count();
        $inProgressCount = (clone $userTasksQuery)->where('status', 'in_progress')->count();
        $completedCount = (clone $userTasksQuery)->where('status', 'completed')->count();

        $totalTasksCount = $pendingCount + $inProgressCount + $completedCount;
        $completionRate = $totalTasksCount > 0 ? round(($completedCount / $totalTasksCount) * 100) : 0;

        // 3. جلب أول سبرنت نشط لمشاريع المستخدم
        $activeSprint = \App\Models\Sprint::whereIn('project_id', $request->user()->projects()->pluck('id'))
            ->where('status', 'active')
            ->with(['project', 'tasks'])
            ->first();

        $sprintProgress = 0;
        $sprintRemainingDays = null;
        if ($activeSprint) {
            $sprintTasksTotal = $activeSprint->tasks()->count();
            $sprintTasksCompleted = $activeSprint->tasks()->where('status', 'completed')->count();
            $sprintProgress = $sprintTasksTotal > 0 ? round(($sprintTasksCompleted / $sprintTasksTotal) * 100) : 0;
            
            if ($activeSprint->end_date) {
                $sprintRemainingDays = now()->startOfDay()->diffInDays($activeSprint->end_date->startOfDay(), false);
            }
        }

        // 4. إحصائيات إضافية لدعم مفهوم المشروع الذكي
        $totalProjectsCount = $request->user()->projects()->count();
        $aiTasksCount = (clone $userTasksQuery)->where('is_ai_generated', true)->count();
        $storyPointsBurned = (clone $userTasksQuery)->where('status', 'completed')->sum('story_points');

        return view('dashboard', compact(
            'tasks',
            'pendingCount',
            'inProgressCount',
            'completedCount',
            'totalTasksCount',
            'completionRate',
            'activeSprint',
            'sprintProgress',
            'sprintRemainingDays',
            'totalProjectsCount',
            'aiTasksCount',
            'storyPointsBurned'
        ));
    }
}
