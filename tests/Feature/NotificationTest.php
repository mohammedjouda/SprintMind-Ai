<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use App\Notifications\TaskCreatedNotification;
use App\Notifications\SprintStartedNotification;
use App\Notifications\TaskDueReminderNotification;
use App\Notifications\SprintDueReminderNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that creating a task triggers TaskCreatedNotification database notification.
     */
    public function test_task_creation_triggers_notification()
    {
        $user = User::factory()->create();
        $project = Project::create([
            'user_id' => $user->id,
            'name' => 'Test Project',
            'category' => 'software',
            'status' => 'active',
        ]);

        $task = Task::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'title' => 'Test Notification Task',
            'description' => 'Verify notification triggers',
            'status' => 'pending',
            'priority' => 'high',
        ]);

        $this->assertEquals(1, $user->unreadNotifications->count());
        $notification = $user->unreadNotifications->first();
        $this->assertEquals(TaskCreatedNotification::class, $notification->type);
        $this->assertEquals('Test Notification Task', $task->title);
    }

    /**
     * Test that activating a sprint triggers SprintStartedNotification.
     */
    public function test_sprint_activation_triggers_notification()
    {
        $user = User::factory()->create();
        $project = Project::create([
            'user_id' => $user->id,
            'name' => 'Test Project',
            'category' => 'software',
            'status' => 'active',
        ]);

        // Sprint Observer is triggered on updated status to active
        $sprint = Sprint::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'name' => 'Test Sprint',
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'status' => 'planned',
            'target_velocity' => 30,
        ]);

        // Initially no notifications
        // Note: UserObserver creates an Inbox project which doesn't count as sprint notification.
        $initialSprintNotifications = $user->notifications()->where('type', SprintStartedNotification::class)->count();
        $this->assertEquals(0, $initialSprintNotifications);

        // Update status to active
        $sprint->status = 'active';
        $sprint->save();

        $activeSprintNotifications = $user->notifications()->where('type', SprintStartedNotification::class)->count();
        $this->assertEquals(1, $activeSprintNotifications);
    }

    /**
     * Test the reminders:send command sends due reminders.
     */
    public function test_reminders_command_sends_notifications()
    {
        $user = User::factory()->create();
        $project = Project::create([
            'user_id' => $user->id,
            'name' => 'Test Project',
            'category' => 'software',
            'status' => 'active',
        ]);

        // Task due tomorrow
        $task = Task::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'title' => 'Task Due Tomorrow',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => Carbon::tomorrow(),
        ]);

        // Sprint ending tomorrow
        $sprint = Sprint::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'name' => 'Sprint Ending Tomorrow',
            'status' => 'active',
            'start_date' => now()->subDays(6),
            'end_date' => Carbon::tomorrow(),
            'target_velocity' => 30,
        ]);

        // Clear creation notifications to assert on reminder notifications
        $user->notifications()->delete();

        // Run Artisan command
        $this->artisan('reminders:send')
            ->expectsOutput("Checking for deadlines on: " . Carbon::tomorrow()->toDateString())
            ->expectsOutput("Found 1 tasks due tomorrow.")
            ->expectsOutput("Found 1 sprints ending tomorrow.")
            ->assertExitCode(0);

        // Check notifications
        $taskDueNotificationCount = $user->notifications()->where('type', TaskDueReminderNotification::class)->count();
        $sprintDueNotificationCount = $user->notifications()->where('type', SprintDueReminderNotification::class)->count();

        $this->assertEquals(1, $taskDueNotificationCount);
        $this->assertEquals(1, $sprintDueNotificationCount);
    }

    /**
     * Test notification controller routing and endpoints.
     */
    public function test_notification_controller_actions()
    {
        $user = User::factory()->create();
        $project = Project::create([
            'user_id' => $user->id,
            'name' => 'Test Project',
            'category' => 'software',
            'status' => 'active',
        ]);

        $task = Task::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'title' => 'Test Task',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $notification = $user->unreadNotifications->first();
        $this->assertNotNull($notification);

        // Index page
        $response = $this->actingAs($user)->get(route('notifications.index'));
        $response->assertStatus(200);

        // Read and Go redirect
        $response = $this->actingAs($user)->get(route('notifications.go', $notification->id));
        $response->assertRedirect(route('tasks.show', $task->id));
        
        // Assert marked read
        $this->assertEquals(0, $user->unreadNotifications()->count());

        // Re-create unread notification
        $user->notifications()->delete();
        $task = Task::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'title' => 'Another Task',
            'status' => 'pending',
            'priority' => 'medium',
        ]);
        $user->refresh();
        $newNotification = $user->unreadNotifications->first();

        // Mark as read endpoint
        $response = $this->actingAs($user)->post(route('notifications.markAsRead', $newNotification->id));
        $response->assertStatus(302); // Redirect back
        $this->assertEquals(0, $user->unreadNotifications()->count());

        // Delete endpoint
        $response = $this->actingAs($user)->delete(route('notifications.destroy', $newNotification->id));
        $response->assertStatus(302);
        $this->assertEquals(0, $user->notifications()->count());
    }

    /**
     * Test user notification preferences update.
     */
    public function test_update_notification_preferences()
    {
        $user = User::factory()->create([
            'notify_task_assigned_email' => true,
            'notify_task_reminder_email' => true,
            'notify_sprint_reminder_email' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('profile.notifications.update'), [
            'notify_task_assigned_email' => false,
            // omission of other fields should toggle them to false because checkbox is unchecked
        ]);

        $response->assertRedirect(route('profile.edit'));
        $user->refresh();

        $this->assertFalse($user->notify_task_assigned_email);
        $this->assertFalse($user->notify_task_reminder_email);
        $this->assertFalse($user->notify_sprint_reminder_email);
    }
}
