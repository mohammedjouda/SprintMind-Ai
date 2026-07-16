<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Sprint;

class SearchController extends Controller
{
    /**
     * Search tasks and sprints for the authenticated user.
     */
    public function search(Request $request)
    {
        $q = $request->query('q');

        if (empty($q) || strlen(trim($q)) < 2) {
            return response()->json([]);
        }

        $user = $request->user();

        // 1. Search tasks belonging to user
        $tasks = $user->tasks()
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%");
            })
            ->with(['project', 'sprint'])
            ->limit(10)
            ->get()
            ->map(function ($task) {
                $subtitleParts = [];
                if ($task->project) {
                    $subtitleParts[] = 'مشروع: ' . $task->project->name;
                }
                if ($task->sprint) {
                    $subtitleParts[] = 'سبرنت: ' . $task->sprint->name;
                }

                return [
                    'id' => $task->id,
                    'type' => 'task',
                    'title' => $task->title,
                    'subtitle' => implode(' | ', $subtitleParts) ?: 'بدون سبرنت أو مشروع',
                    'status' => $task->status,
                    'url' => route('tasks.show', $task->id),
                ];
            });

        // 2. Search sprints belonging to user
        $sprints = $user->sprints()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('goal', 'like', "%{$q}%");
            })
            ->with('project')
            ->limit(10)
            ->get()
            ->map(function ($sprint) {
                $subtitleParts = [];
                if ($sprint->project) {
                    $subtitleParts[] = 'مشروع: ' . $sprint->project->name;
                }
                if ($sprint->goal) {
                    $subtitleParts[] = 'الهدف: ' . $sprint->goal;
                }

                return [
                    'id' => $sprint->id,
                    'type' => 'sprint',
                    'title' => $sprint->name,
                    'subtitle' => implode(' | ', $subtitleParts) ?: 'بدون هدف محدد',
                    'status' => $sprint->status,
                    'url' => route('sprints.show', $sprint->id),
                ];
            });

        // Combine task and sprint results
        $results = $tasks->concat($sprints);

        return response()->json($results);
    }
}
