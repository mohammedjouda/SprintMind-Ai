<?php

namespace App\Notifications;

use App\Models\Sprint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SprintStartedNotification extends Notification
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
            ->subject('بدء السبرنت الذكي: ' . $this->sprint->name)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم تفعيل وبدء سبرنت جديد بنجاح.')
            ->line('اسم السبرنت: ' . $this->sprint->name)
            ->line('هدف السبرنت: ' . ($this->sprint->goal ?? 'لا يوجد هدف محدد'))
            ->action('عرض السبرنت والكانبان', route('sprints.show', $this->sprint->id))
            ->line('حظاً موفقاً في تحقيق أهداف السبرنت! 🚀');
    }

    public function toArray($notifiable): array
    {
        return [
            'sprint_id' => $this->sprint->id,
            'title' => 'بدء سبرنت جديد',
            'message' => 'تم تفعيل السبرنت "' . $this->sprint->name . '" بنجاح. جاهز للانطلاق؟ 🚀',
            'type' => 'sprint_started',
            'action_url' => route('sprints.show', $this->sprint->id, false),
        ];
    }
}
