<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirebaseService
{
    // دالة لإرسال الإشعار إلى Firebase
    public function sendNotification($deviceToken, $title, $message)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = [
            'Authorization' => 'key=' . env('FIREBASE_SERVER_KEY'),
            'Content-Type' => 'application/json',
        ];

        $data = [
            "to" => $deviceToken,  // التوكن الخاص بالجهاز (يتم الحصول عليه من التطبيق)
            "notification" => [
                "title" => $title,
                "body" => $message,
                "sound" => "default",
            ],
            "priority" => "high",
            "content_available" => true,
        ];

        $response = Http::withHeaders($headers)
                        ->post($url, $data);

        return $response->json();
    }
}
