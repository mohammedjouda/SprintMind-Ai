<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TaskCreatedNotification extends Notification
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

        if ($notifiable->notify_task_assigned_email) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('مهمة جديدة مسندة: ' . $this->task->title)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم إسناد مهمة جديدة إليك في نظام SprintMind-Ai.')
            ->line('عنوان المهمة: ' . $this->task->title)
            ->line('الأولوية: ' . str_replace('_', ' ', $this->task->priority))
            ->action('عرض المهمة', route('tasks.show', $this->task->id))
            ->line('شكراً لاستخدامك تطبيقنا!');
    }

    public function toArray($notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => 'مهمة جديدة مسندة إليك',
            'message' => 'تم إسناد المهمة "' . $this->task->title . '" إليك.',
            'type' => 'task_created',
            'action_url' => route('tasks.show', $this->task->id, false),
        ];
    }
}
