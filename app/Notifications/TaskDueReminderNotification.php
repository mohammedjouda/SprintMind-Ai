<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TaskDueReminderNotification extends Notification
{
    use Queueable;

    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->notify_task_reminder_email) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تذكير استحقاق مهمة: ' . $this->task->title)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('هذا تذكير بأن المهمة التالية تستحق غداً:')
            ->line('عنوان المهمة: ' . $this->task->title)
            ->line('تاريخ الاستحقاق: ' . $this->task->due_date->format('Y-m-d'))
            ->action('عرض المهمة', route('tasks.show', $this->task->id))
            ->line('يرجى إكمال المهمة أو تحديث حالتها في أقرب وقت.');
    }

    public function toArray($notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => 'تذكير بموعد استحقاق مهمة',
            'message' => 'المهمة "' . $this->task->title . '" تستحق غداً.',
            'type' => 'task_due',
            'action_url' => route('tasks.show', $this->task->id, false),
        ];
    }
}
