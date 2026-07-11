<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\AcceptanceCriteria;
use App\Services\AIAgileArchitectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AIPromptController extends Controller
{
    /**
     * توليد هيكل المشروع المبدئي في الذاكرة وإرجاعه كـ JSON للمعاينة والتعديل
     */
    public function preview(Request $request, AIAgileArchitectService $aiService)
    {
        $request->validate([
            'ai_prompt' => 'required|string|max:500',
        ]);

        try {
            $scaffold = $aiService->generateScaffold($request->input('ai_prompt'));
            return response()->json($scaffold);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * حفظ المشروع والسبرنت والمهام المعدلة والموافق عليها نهائياً
     */
    public function save(Request $request)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'sprint_name' => 'required|string|max:255',
            'sprint_goal' => 'nullable|string|max:500',
            'tasks' => 'required|array',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string|max:2000',
            'tasks.*.priority' => 'required|in:low,medium,high',
            'tasks.*.story_points' => 'required|integer|min:0',
            'tasks.*.criteria' => 'nullable|array',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. إنشاء المشروع
                $project = $request->user()->projects()->create([
                    'name' => $request->input('project_name'),
                    'category' => $request->input('category'),
                    'expected_duration' => '1_month',
                    'description' => "مساحة عمل ذكية تمت مراجعتها وحفظها بنجاح.",
                    'use_ai_scaffold' => true,
                    'status' => 'active',
                ]);

                // 2. إنشاء السبرنت وتفعيله كـ active
                $sprint = $project->sprints()->create([
                    'name' => $request->input('sprint_name'),
                    'goal' => $request->input('sprint_goal'),
                    'start_date' => now(),
                    'end_date' => now()->addDays(14),
                    'status' => 'active',
                ]);

                // 3. إنشاء المهام التابعة للمشروع والسبرنت
                $tasksData = $request->input('tasks');
                foreach ($tasksData as $index => $tData) {
                    $task = $project->tasks()->create([
                        'sprint_id' => $sprint->id,
                        'user_id' => $request->user()->id,
                        'title' => $tData['title'],
                        'description' => $tData['description'] ?? null,
                        'status' => ($index === 0) ? 'in_progress' : 'pending', // أول مهمة In Progress للجمال البصري
                        'priority' => $tData['priority'],
                        'story_points' => $tData['story_points'] ?? 0,
                        'is_ai_generated' => true,
                    ]);

                    // إضافة معايير القبول
                    if (!empty($tData['criteria'])) {
                        foreach ($tData['criteria'] as $crit) {
                            if (!empty(trim($crit))) {
                                $task->acceptanceCriteria()->create([
                                    'title' => trim($crit),
                                    'is_completed' => false,
                                ]);
                            }
                        }
                    }
                }
            });

            // وميض رسالة النجاح في الجلسة للمستخدم
            $request->session()->flash('success', 'تم تأسيس وحفظ مساحة عمل مشروعك وسبرنت المهام بنجاح! 🎉');

            return response()->json([
                'success' => true,
                'redirect_url' => route('dashboard'),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AI save workspace error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء حفظ مساحة العمل: ' . $e->getMessage(),
            ], 500);
        }
    }
}
