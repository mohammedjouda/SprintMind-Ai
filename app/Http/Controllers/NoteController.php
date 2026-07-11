<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Project;
use App\Models\Task;
use App\Services\AIAgileArchitectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    /**
     * Display a listing of the notes.
     */
    public function index(Request $request)
    {
        $notes = $request->user()->notes()
            ->orderBy('is_pinned', 'desc')
            ->latest()
            ->get();

        // Extract unique tags for sidebar filters
        $allTags = $notes->pluck('tags')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Get user's projects for select dropdowns
        $projects = $request->user()->projects()
            ->select('id', 'name')
            ->latest()
            ->get();

        return view('notes.index', compact('notes', 'allTags', 'projects'));
    }

    /**
     * Store a newly created note in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'project_id' => 'nullable|exists:projects,id',
            'is_pinned' => 'boolean',
            'color' => 'nullable|string|max:30',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        $request->user()->notes()->create([
            'title' => $request->title,
            'content' => $request->content,
            'project_id' => $request->project_id,
            'is_pinned' => $request->boolean('is_pinned', false),
            'color' => $request->color ?? 'default',
            'tags' => $request->tags ?? [],
        ]);

        return redirect()->route('notes.index')->with('success', 'تم حفظ الملاحظة بنجاح! 📝');
    }

    /**
     * Update the specified note in storage.
     */
    public function update(Request $request, Note $note)
    {
        if ($note->user_id !== $request->user()->id) {
            abort(403, 'غير مصرح لك بتعديل هذه الملاحظة.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'project_id' => 'nullable|exists:projects,id',
            'is_pinned' => 'boolean',
            'color' => 'nullable|string|max:30',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        $note->update([
            'title' => $request->title,
            'content' => $request->content,
            'project_id' => $request->project_id,
            'is_pinned' => $request->boolean('is_pinned', false),
            'color' => $request->color ?? 'default',
            'tags' => $request->tags ?? [],
        ]);

        return redirect()->route('notes.index')->with('success', 'تم تحديث الملاحظة بنجاح! 📝');
    }

    /**
     * Remove the specified note from storage.
     */
    public function destroy(Request $request, Note $note)
    {
        if ($note->user_id !== $request->user()->id) {
            abort(403, 'غير مصرح لك بحذف هذه الملاحظة.');
        }

        $note->delete();

        return redirect()->route('notes.index')->with('success', 'تم حذف الملاحظة بنجاح! 🗑️');
    }

    /**
     * Toggle the pinned status of a note.
     */
    public function togglePin(Request $request, Note $note)
    {
        if ($note->user_id !== $request->user()->id) {
            return response()->json(['error' => 'غير مصرح لك بالعملية.'], 403);
        }

        $note->update([
            'is_pinned' => !$note->is_pinned
        ]);

        return response()->json([
            'success' => true,
            'is_pinned' => $note->is_pinned
        ]);
    }

    /**
     * Request Google Gemini to analyze a note.
     */
    public function analyze(Request $request, AIAgileArchitectService $aiService)
    {
        $request->validate([
            'content' => 'required|string',
            'mode' => 'required|in:tasks,project',
        ]);

        try {
            $result = $aiService->analyzeNote($request->content, $request->mode);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Note analysis failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'فشل في تحليل الملاحظة بالذكاء الاصطناعي: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Commit approved tasks into the database backlog.
     */
    public function commitTasks(Request $request)
    {
        $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'new_project_name' => 'nullable|required_without:project_id|string|max:255',
            'new_project_category' => 'nullable|required_without:project_id|in:software,marketing,personal',
            'tasks' => 'required|array|min:1',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.priority' => 'required|in:low,medium,high',
            'tasks.*.story_points' => 'required|integer|in:1,2,3,5,8',
            'tasks.*.acceptance_criteria' => 'nullable|string',
        ]);

        try {
            $count = DB::transaction(function () use ($request) {
                $projectId = $request->project_id;

                // If project_id is not selected, we create a new project
                if (empty($projectId)) {
                    $project = $request->user()->projects()->create([
                        'name' => $request->new_project_name,
                        'category' => $request->new_project_category ?? 'software',
                        'expected_duration' => '1_month',
                        'description' => 'مشروع تم إنشاؤه تلقائياً من الملاحظة الذكية.',
                        'use_ai_scaffold' => true,
                        'status' => 'active',
                    ]);
                    $projectId = $project->id;
                }

                $tasksData = $request->input('tasks');
                foreach ($tasksData as $tData) {
                    $task = Task::create([
                        'project_id' => $projectId,
                        'sprint_id' => null, // backlog routing
                        'user_id' => $request->user()->id,
                        'title' => $tData['title'],
                        'description' => 'تم استخراجه من الملاحظة.',
                        'status' => 'pending',
                        'priority' => $tData['priority'] ?? 'medium',
                        'story_points' => (int)($tData['story_points'] ?? 1),
                        'due_date' => null,
                        'start_date' => null,
                        'is_ai_generated' => true,
                    ]);

                    if (!empty($tData['acceptance_criteria'])) {
                        $task->acceptanceCriteria()->create([
                            'title' => trim($tData['acceptance_criteria']),
                            'is_completed' => false,
                        ]);
                    }
                }

                return count($tasksData);
            });

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Commit notes tasks transaction failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'حدث خطأ أثناء حفظ المهام: ' . $e->getMessage()
            ], 500);
        }
    }
}
