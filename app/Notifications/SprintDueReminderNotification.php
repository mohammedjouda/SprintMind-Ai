<?php

namespace App\Notifications;

use App\Models\Sprint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SprintDueReminderNotification extends Notification
{
    use Queueable;

    protected $sprint;

    public function __construct(Sprint $sprint)
    {
        $this->sprint = $sprint;
    }

    public function via($notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->notify_sprint_reminder_email) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تذكير انتهاء السبرنت: ' . $this->sprint->name)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('هذا تذكير بأن السبرنت النشط التالي ينتهي غداً:')
            ->line('اسم السبرنت: ' . $this->sprint->name)
            ->line('تاريخ الانتهاء: ' . $this->sprint->end_date->format('Y-m-d'))
            ->action('عرض السبرنت والكانبان', route('sprints.show', $this->sprint->id))
            ->line('يرجى مراجعة المهام المتبقية وإغلاقها.');
    }

    public function toArray($notifiable): array
    {
        return [
            'sprint_id' => $this->sprint->id,
            'title' => 'تذكير بانتهاء السبرنت',
            'message' => 'السبرنت "' . $this->sprint->name . '" ينتهي غداً.',
            'type' => 'sprint_due',
            'action_url' => route('sprints.show', $this->sprint->id, false),
        ];
    }
}
