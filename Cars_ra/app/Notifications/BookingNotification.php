<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BookingNotification extends Notification
{
    use Queueable;

    public $booking;
    public $status;

    public function __construct($booking, $status)
    {
        $this->booking = $booking;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail']; // أو ['database'] حسب الإشعار المطلوب
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('حالة حجزك: ' . $this->status)
                    ->action('عرض الحجز', url('/'))
                    ->line('شكراً لاستخدامك موقعنا!');
    }
}
