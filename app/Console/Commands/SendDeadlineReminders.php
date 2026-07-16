<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\Sprint;
use App\Notifications\TaskDueReminderNotification;
use App\Notifications\SprintDueReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send deadline reminders for tasks and sprints due tomorrow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        $this->info("Checking for deadlines on: {$tomorrow}");

        // 1. Task Reminders
        $tasks = Task::where('status', '!=', 'completed')
            ->whereDate('due_date', $tomorrow)
            ->with('assignee')
            ->get();

        $this->info("Found {$tasks->count()} tasks due tomorrow.");

        foreach ($tasks as $task) {
            if ($task->assignee) {
                $task->assignee->notify(new TaskDueReminderNotification($task));
                $this->line("Sent reminder to {$task->assignee->name} for task: {$task->title}");
            }
        }

        // 2. Sprint Reminders
        $sprints = Sprint::where('status', 'active')
            ->whereDate('end_date', $tomorrow)
            ->with('user')
            ->get();

        $this->info("Found {$sprints->count()} sprints ending tomorrow.");

        foreach ($sprints as $sprint) {
            if ($sprint->user) {
                $sprint->user->notify(new SprintDueReminderNotification($sprint));
                $this->line("Sent reminder to {$sprint->user->name} for sprint: {$sprint->name}");
            }
        }

        $this->info('Deadline reminders processed successfully.');
    }
}
