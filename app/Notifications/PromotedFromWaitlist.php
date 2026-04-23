<?php

namespace App\Notifications;

use App\Models\Workshop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class PromotedFromWaitlist extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Workshop $workshop)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush(object $notifiable, mixed $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Good News! 🎉')
            ->body("You have been promoted from the waitlist for \"{$this->workshop->title}\". Your spot is now confirmed!")
            ->action('View My Schedule', 'view_schedule')
            ->data(['url' => route('my-schedule')]);
    }
}
