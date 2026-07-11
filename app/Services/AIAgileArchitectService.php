<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIAgileArchitectService
{
    /**
     * Generate agile workspace scaffold based on a user prompt.
     *
     * @param string $userPrompt
     * @return array
     * @throws \Exception
     */
    public function generateScaffold(string $userPrompt): array
    {
        $apiKey = config('services.gemini.key');
        $model = config('services.gemini.model', 'gemini-3.5-flash');

        if (empty($apiKey)) {
            throw new \Exception('Google Gemini API Key is not configured in services config.');
        }

        try {
            $systemPrompt = "You are a Senior Scrum Master and Technical Architect.\n" .
                "Based on the user's prompt, generate an agile workspace scaffold containing:\n" .
                "1. A Project Name\n" .
                "2. A Category (MUST be exactly 'software', 'marketing', or 'personal')\n" .
                "3. An active Sprint Name (e.g., 'Sprint 1: Core Setup')\n" .
                "4. A Sprint Goal\n" .
                "5. A list of Tasks. Each task should have an action-oriented title, description, priority ('high', 'medium', or 'low'), story points (Fibonacci number: 1, 2, 3, 5, 8), and a list of criteria (acceptance criteria).\n\n" .
                "You must respond ONLY with a valid JSON object matching this exact schema:\n" .
                "{\n" .
                "  \"project_name\": \"string\",\n" .
                "  \"category\": \"software|marketing|personal\",\n" .
                "  \"sprint_name\": \"string\",\n" .
                "  \"sprint_goal\": \"string\",\n" .
                "  \"tasks\": [\n" .
                "    {\n" .
                "      \"title\": \"string\",\n" .
                "      \"description\": \"string\",\n" .
                "      \"priority\": \"high|medium|low\",\n" .
                "      \"story_points\": 1,\n" .
                "      \"criteria\": [\"string\", \"string\"]\n" .
                "    }\n" .
                "  ]\n" .
                "}";

            $fullPrompt = $systemPrompt . "\n\nUser Prompt: " . $userPrompt;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
                ->timeout(60)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $fullPrompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

            if ($response->failed()) {
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? $response->body() ?: 'Unknown Gemini API error';
                throw new \Exception("Gemini API Error [Status: {$response->status()}]: {$errorMessage}");
            }

            $data = $response->json();
            $messageContent = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($messageContent)) {
                throw new \Exception("Gemini API responded with an empty content choice.");
            }

            $scaffold = json_decode($messageContent, true);

            if (json_last_error() !== JSON_ERROR_NONE || !$scaffold || !isset($scaffold['project_name']) || !isset($scaffold['tasks'])) {
                throw new \Exception("Invalid JSON format received from AI provider.");
            }

            // Ensure category is valid
            if (!in_array($scaffold['category'] ?? '', ['software', 'marketing', 'personal'])) {
                $scaffold['category'] = 'software';
            }

            return $scaffold;
        } catch (\Exception $e) {
            Log::error('AI generateScaffold exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Auto schedule unscheduled tasks using Google Gemini API.
     *
     * @param \Illuminate\Support\Collection $unscheduledTasks
     * @param string $startDate
     * @return array
     * @throws \Exception
     */
    public function autoScheduleTasks($unscheduledTasks, string $startDate): array
    {
        $apiKey = config('services.gemini.key');
        $model = config('services.gemini.model', 'gemini-3.5-flash');
        if ($model === 'gemini-3.5-flash') {
            $model = 'gemini-3.5-flash';
        }

        if (empty($apiKey)) {
            throw new \Exception('Google Gemini API Key is not configured in services config.');
        }

        $tasksData = $unscheduledTasks->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'priority' => $task->priority ?? 'medium',
                'story_points' => (int) ($task->story_points ?? 0),
            ];
        })->toArray();

        try {
            $systemPrompt = "You are an AI Productivity Copilot.\n" .
                "Your goal is to distribute unscheduled tasks across weekdays, starting from a given start date.\n" .
                "STRICT CONSTRAINTS:\n" .
                "1. Weekdays are strictly Sunday to Thursday. Do NOT schedule any tasks on Friday or Saturday.\n" .
                "2. Do not exceed a maximum capacity of 8 Story Points per day for the sum of all tasks scheduled on that day.\n" .
                "3. High priority tasks must be scheduled first (earliest possible days).\n" .
                "4. For each task, output a start_date and due_date. If a task spans 1 day, start_date and due_date should be the same. If it spans multiple days, adjust accordingly. Usually, tasks should be scheduled for a single day.\n\n" .
                "You must respond ONLY with a valid JSON array of objects matching this exact schema:\n" .
                "[\n" .
                "  {\n" .
                "    \"task_id\": 123,\n" .
                "    \"start_date\": \"YYYY-MM-DD\",\n" .
                "    \"due_date\": \"YYYY-MM-DD\"\n" .
                "  }\n" .
                "]";

            $fullPrompt = $systemPrompt . "\n\n" .
                "Start Date: " . $startDate . "\n" .
                "Unscheduled Tasks JSON:\n" .
                json_encode($tasksData, JSON_PRETTY_PRINT);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
                ->timeout(60)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $fullPrompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

            if ($response->failed()) {
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? $response->body() ?: 'Unknown Gemini API error';
                throw new \Exception("Gemini API Error [Status: {$response->status()}]: {$errorMessage}");
            }

            $data = $response->json();
            $messageContent = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($messageContent)) {
                throw new \Exception("Gemini API responded with an empty content choice.");
            }

            $schedule = json_decode($messageContent, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($schedule)) {
                throw new \Exception("Invalid JSON format received from AI provider.");
            }

            return $schedule;
        } catch (\Exception $e) {
            Log::error('AI autoScheduleTasks exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Analyze note and convert it into structured Agile items.
     *
     * @param string $content
     * @param string $mode
     * @return array
     * @throws \Exception
     */
    public function analyzeNote(string $content, string $mode = 'tasks'): array
    {
        $apiKey = config('services.gemini.key');
        $model = config('services.gemini.model', 'gemini-3.5-flash');

        if (empty($apiKey)) {
            throw new \Exception('Google Gemini API Key is not configured in services config.');
        }

        try {
            $systemPrompt = "You are a Technical Agile Product Owner.\n" .
                "Analyze the provided note and convert it into structured Agile items based on the requested mode:\n";

            if ($mode === 'project') {
                $systemPrompt .= "Mode is 'project': Scaffold a brand-new project with a suggested name, category, and initial backlog tasks.\n" .
                    "You must respond ONLY with a valid JSON object matching this exact schema:\n" .
                    "{\n" .
                    "  \"summary\": \"A brief summary of the project derived from the note\",\n" .
                    "  \"suggested_action\": \"project\",\n" .
                    "  \"project_name\": \"Suggested Project Name\",\n" .
                    "  \"category\": \"software|marketing|personal\",\n" .
                    "  \"items\": [\n" .
                    "    {\n" .
                    "      \"temp_id\": 1,\n" .
                    "      \"title\": \"Action-oriented task title\",\n" .
                    "      \"priority\": \"high|medium|low\",\n" .
                    "      \"story_points\": 1,\n" .
                    "      \"acceptance_criteria\": \"Criteria that defines completion\"\n" .
                    "    }\n" .
                    "  ]\n" .
                    "}";
            } else {
                $systemPrompt .= "Mode is 'tasks': Extract backlog tasks for an existing project.\n" .
                    "You must respond ONLY with a valid JSON object matching this exact schema:\n" .
                    "{\n" .
                    "  \"summary\": \"A brief summary of the tasks derived from the note\",\n" .
                    "  \"suggested_action\": \"tasks\",\n" .
                    "  \"items\": [\n" .
                    "    {\n" .
                    "      \"temp_id\": 1,\n" .
                    "      \"title\": \"Action-oriented task title\",\n" .
                    "      \"priority\": \"high|medium|low\",\n" .
                    "      \"story_points\": 1,\n" .
                    "      \"acceptance_criteria\": \"Criteria that defines completion\"\n" .
                    "    }\n" .
                    "  ]\n" .
                    "}";
            }

            $systemPrompt .= "\n\nSTRICT CONSTRAINTS:\n" .
                "1. Return pure, valid JSON ONLY. Do not include markdown formatting or extra text.\n" .
                "2. Ensure story_points is one of 1, 2, 3, 5, 8.\n" .
                "3. priority must be exactly 'high', 'medium', or 'low'.";

            $fullPrompt = $systemPrompt . "\n\nNote Content:\n" . $content;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
                ->timeout(60)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $fullPrompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

            if ($response->failed()) {
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? $response->body() ?: 'Unknown Gemini API error';
                throw new \Exception("Gemini API Error [Status: {$response->status()}]: {$errorMessage}");
            }

            $data = $response->json();
            $messageContent = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($messageContent)) {
                throw new \Exception("Gemini API responded with an empty content choice.");
            }

            $cleanedContent = trim($messageContent);
            if (str_starts_with($cleanedContent, '```')) {
                $cleanedContent = preg_replace('/^```(?:json)?\s+/i', '', $cleanedContent);
                $cleanedContent = preg_replace('/\s+```$/', '', $cleanedContent);
            }

            $result = json_decode(trim($cleanedContent), true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($result)) {
                throw new \Exception("Invalid JSON format received from AI provider.");
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('AI analyzeNote exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Plan a sprint based on unscheduled tasks, target velocity, and duration.
     *
     * @param \Illuminate\Support\Collection $unscheduledTasks
     * @param int $targetVelocity
     * @param int $durationWeeks
     * @return array
     * @throws \Exception
     */
    public function planSprint($unscheduledTasks, int $targetVelocity, int $durationWeeks): array
    {
        $apiKey = config('services.gemini.key');
        $model = config('services.gemini.model', 'gemini-3.5-flash');

        if (empty($apiKey)) {
            throw new \Exception('Google Gemini API Key is not configured in services config.');
        }

        $tasksData = $unscheduledTasks->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'priority' => $task->priority ?? 'medium',
                'story_points' => (int) ($task->story_points ?? 0),
            ];
        })->toArray();

        try {
            $systemPrompt = "You are an expert Agile Scrum Master.\n" .
                "Analyze the provided backlog tasks (IDs, titles, priorities, story points).\n" .
                "Select an optimal bundle of cohesive tasks that fit within the target velocity limit.\n" .
                "STRICT CONSTRAINTS:\n" .
                "1. The sum of story_points of selected tasks MUST NOT exceed target velocity ($targetVelocity points).\n" .
                "2. Prioritize high-priority tasks and logical technical dependencies.\n" .
                "3. Respond ONLY with a valid JSON object matching this exact schema:\n" .
                "{\n" .
                "  \"sprint_name\": \"string (A creative sprint name in Arabic based on the theme of selected tasks)\",\n" .
                "  \"sprint_goal\": \"string (A concise sprint goal in Arabic outlining the business/technical value)\",\n" .
                "  \"selected_task_ids\": [1, 2, 5],\n" .
                "  \"total_points\": 12,\n" .
                "  \"reasoning\": \"string (A brief explanation in Arabic of why these tasks were grouped together)\"\n" .
                "}";

            $fullPrompt = $systemPrompt . "\n\n" .
                "Target Velocity: " . $targetVelocity . " story points\n" .
                "Duration: " . $durationWeeks . " weeks\n" .
                "Unscheduled Backlog Tasks JSON:\n" .
                json_encode($tasksData, JSON_PRETTY_PRINT);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
                ->timeout(60)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $fullPrompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

            if ($response->failed()) {
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? $response->body() ?: 'Unknown Gemini API error';
                throw new \Exception("Gemini API Error [Status: {$response->status()}]: {$errorMessage}");
            }

            $data = $response->json();
            $messageContent = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($messageContent)) {
                throw new \Exception("Gemini API responded with an empty content choice.");
            }

            $cleanedContent = trim($messageContent);
            if (str_starts_with($cleanedContent, '```')) {
                $cleanedContent = preg_replace('/^```(?:json)?\s+/i', '', $cleanedContent);
                $cleanedContent = preg_replace('/\s+```$/', '', $cleanedContent);
            }

            $result = json_decode(trim($cleanedContent), true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($result)) {
                throw new \Exception("Invalid JSON format received from AI provider.");
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('AI planSprint exception: ' . $e->getMessage());
            // Fallback
            $fallbackIds = [];
            $currentSum = 0;
            foreach ($unscheduledTasks as $task) {
                $points = (int) ($task->story_points ?? 0);
                if ($currentSum + $points <= $targetVelocity) {
                    $fallbackIds[] = $task->id;
                    $currentSum += $points;
                }
            }

            return [
                'sprint_name' => 'سبرنت مخطط تلقائياً 🚀',
                'sprint_goal' => 'التركيز على إنجاز مجموعة من المهام العالية الأهمية واستغلال سعة السبرنت بأفضل شكل.',
                'selected_task_ids' => $fallbackIds,
                'total_points' => $currentSum,
                'reasoning' => 'واجهنا صعوبة في الاتصال بمستشار الذكاء الاصطناعي، لذا قمنا بجدولة المهام تلقائياً وترتيبها لك بناءً على الأولوية والسعة المتاحة لتبدأ العمل مباشرة.',
            ];
        }
    }

    /**
     * Analyze a sprint's health and timelines.
     *
     * @param \App\Models\Sprint $sprint
     * @return array
     */
    public function analyzeSprintHealth($sprint): array
    {
        $apiKey = config('services.gemini.key');
        $model = config('services.gemini.model', 'gemini-3.5-flash');

        if (empty($apiKey)) {
            return [
                'health_status' => 'warning',
                'bottleneck_detected' => false,
                'copilot_advice' => 'مفتاح API الخاص بـ Gemini غير مهيأ. يرجى إضافته إلى إعدادات النظام.',
            ];
        }

        // Gather metrics
        $startDate = $sprint->start_date ?: now();
        $endDate = $sprint->end_date ?: now()->addWeeks(2);
        $totalDays = max(1, $startDate->diffInDays($endDate));
        $daysElapsed = max(0, $startDate->diffInDays(now()));
        $daysRemaining = max(0, now()->diffInDays($endDate));

        $tasks = $sprint->tasks;
        $totalTasks = $tasks->count();

        $todoTasks = $tasks->where('status', 'todo')->count();
        $inProgressTasks = $tasks->where('status', 'in_progress')->count();
        $reviewTasks = $tasks->where('status', 'review')->count();
        $completedTasks = $tasks->where('status', 'completed')->count();

        $todoPoints = $tasks->where('status', 'todo')->sum('story_points');
        $inProgressPoints = $tasks->where('status', 'in_progress')->sum('story_points');
        $reviewPoints = $tasks->where('status', 'review')->sum('story_points');
        $completedPoints = $tasks->where('status', 'completed')->sum('story_points');
        $totalPoints = $tasks->sum('story_points');

        $completionPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        try {
            $systemPrompt = "You are a friendly, encouraging Agile Tech Lead Co-pilot.\n" .
                "STRICT RULE: NEVER use robotic, academic, or boring formal Agile jargon like 'وضع مستقر' or 'مبادئ السكرام'. Speak directly to the developer in natural, engaging, professional Arabic (using warm and encouraging words like 'يا بطل', 'عاش', 'ممتاز', or clear urgent warnings like 'يا صديقي متبقي...').\n" .
                "DATA-DRIVEN RULE: You MUST incorporate the actual numbers provided below in your concise 2-sentence advice (e.g., mention the exact number of days remaining, completed tasks, tasks in progress, or tasks waiting in code review).\n" .
                "Analyze the timeline, completion percentage, and task distributions to flag warnings or bottlenecks.\n" .
                "You must respond ONLY with a valid JSON object matching this exact schema:\n" .
                "{\n" .
                "  \"health_status\": \"healthy|warning|critical\",\n" .
                "  \"bottleneck_detected\": true|false,\n" .
                "  \"copilot_advice\": \"string (Your human-like Arabic advice with relevant emojis incorporating the actual metrics)\"\n" .
                "}";

            $fullPrompt = $systemPrompt . "\n\n" .
                "Sprint: " . $sprint->name . "\n" .
                "Sprint Goal: " . $sprint->goal . "\n" .
                "Timeline & Metrics:\n" .
                "- Total duration: $totalDays days\n" .
                "- Days elapsed: $daysElapsed days\n" .
                "- Days remaining: $daysRemaining days\n" .
                "- Total Tasks: $totalTasks tasks ($totalPoints story points)\n" .
                "- Completed Tasks: $completedTasks tasks ($completedPoints story points)\n" .
                "- In-Progress Tasks: $inProgressTasks tasks ($inProgressPoints story points)\n" .
                "- Review Tasks: $reviewTasks tasks ($reviewPoints story points)\n" .
                "- Todo Tasks: $todoTasks tasks ($todoPoints story points)\n" .
                "- Completion Percentage: $completionPercentage%";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
                ->timeout(60)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $fullPrompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

            if ($response->failed()) {
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? $response->body() ?: 'Unknown Gemini API error';
                throw new \Exception("Gemini API Error [Status: {$response->status()}]: {$errorMessage}");
            }

            $data = $response->json();
            $messageContent = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($messageContent)) {
                throw new \Exception("Gemini API responded with an empty content choice.");
            }

            $cleanedContent = trim($messageContent);
            if (str_starts_with($cleanedContent, '```')) {
                $cleanedContent = preg_replace('/^```(?:json)?\s+/i', '', $cleanedContent);
                $cleanedContent = preg_replace('/\s+```$/', '', $cleanedContent);
            }

            $result = json_decode(trim($cleanedContent), true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($result)) {
                throw new \Exception("Invalid JSON format received from AI provider.");
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('AI analyzeSprintHealth exception: ' . $e->getMessage());

            // Simple rule-based fallback with dynamic counts
            $health = 'healthy';
            $bottleneck = false;
            $advice = "عاش يا بطل! السبرنت مستمر بشكل رائع وتم إنجاز {$completedTasks} من أصل {$totalTasks} مهام حتى الآن. واصل هذا التقدم المميز! 🌟";

            if ($daysRemaining > 0 && $completionPercentage < 30 && $daysElapsed > ($totalDays * 0.5)) {
                $health = 'warning';
                $advice = "انتبه يا صديقي، لقد مر {$daysElapsed} أيام من السبرنت ومعدل الإنجاز هو {$completionPercentage}% فقط. نحتاج للتركيز على {$inProgressTasks} مهام قيد العمل لنعوض فارق الوقت! ⏳";
            }

            if ($reviewTasks >= 3) {
                $health = 'warning';
                $bottleneck = true;
                $advice = "يبدو أن لدينا تكدس بوجود {$reviewTasks} مهام معلقة في مراجعة الكود (Code Review). ما رأيك أن ننتهي منها اليوم حتى ننطلق في بقية المهام؟ 🔍";
            }

            if ($daysRemaining <= 2 && $completionPercentage < 70) {
                $health = 'critical';
                $advice = "موقف حرج يا بطل! متبقي يومان فقط على نهاية السبرنت وهناك {$todoTasks} مهام لم تبدأ بعد. دعنا نحدد أولوياتنا لإنقاذ السبرنت! 💪";
            }

            return [
                'health_status' => $health,
                'bottleneck_detected' => $bottleneck,
                'copilot_advice' => $advice,
            ];
        }
    }
}
