<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification
{
    use Queueable;

    protected $booking;

    // نستقبل الحجز لإظهار تفاصيله داخل الإشعار
    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    // نحدد أن الإشعار يُخزن في قاعدة البيانات
    public function via($notifiable)
    {
        return ['database'];
    }

    // البيانات التي تُخزن داخل عمود "data"
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'تم تأكيد الحجز للسيارة: ' . $this->booking->car->model . ' بنجاح',
            'booking_id' => $this->booking->id,
            'car_name' => $this->booking->car->model ?? 'سيارة غير معروفة',
        ];
    }
}
