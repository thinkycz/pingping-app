<?php

namespace App\Notifications;

use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MonitorStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public Monitor $monitor;

    public string $newStatus;

    public function __construct(Monitor $monitor, string $newStatus)
    {
        $this->monitor = $monitor;
        $this->newStatus = $newStatus;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = __('monitoring.status.'.strtolower($this->newStatus));

        return (new MailMessage)
            ->subject(__('notifications.monitor_subject', ['url' => $this->monitor->url, 'status' => $status]))
            ->greeting(__('notifications.greeting'))
            ->line(__('notifications.monitor_line', ['url' => $this->monitor->url, 'status' => $status]))
            ->when(
                $this->newStatus === 'Down' && $this->monitor->failure_code,
                fn (MailMessage $mail): MailMessage => $mail->line(
                    __('monitoring.failures.'.$this->monitor->failure_code),
                ),
            )
            ->action(__('notifications.view_monitor'), route('monitors.show', $this->monitor))
            ->line(__('notifications.thanks'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'monitor_id' => $this->monitor->id,
            'status' => $this->newStatus,
        ];
    }
}
