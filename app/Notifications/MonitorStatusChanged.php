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
        $color = $this->newStatus === 'Up' ? 'success' : 'error';
        $message = "Your monitor for {$this->monitor->url} is now {$this->newStatus}.";

        return (new MailMessage)
            ->subject("Monitor Status Changed: {$this->monitor->url} is {$this->newStatus}")
            ->greeting("Hello!")
            ->line($message)
            ->action('View Dashboard', url('/dashboard'))
            ->line('Thank you for using PingPing!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'monitor_id' => $this->monitor->id,
            'status' => $this->newStatus,
        ];
    }
}
