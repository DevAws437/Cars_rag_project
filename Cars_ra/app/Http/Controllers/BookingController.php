<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Car;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BookingNotification;
use App\Notifications\BookingConfirmed;

class BookingController extends Controller
{


    public function confirmBooking($id)
    {
        // جلب الحجز مع علاقته بالمستخدم
        $booking = Booking::with('user', 'car')->find($id);

        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }

        // تحديث حالة الحجز إلى confirmed
        $booking->status = 'confirmed';
        $booking->save();

        // إرسال الإشعار للمستخدم المرتبط بالحجز
        $user = $booking->user;
        $user->notify(new BookingConfirmed($booking));

        return response()->json([
            'message' => 'تم تأكيد الحجز للسيارة: ' . $booking->car->model . ' بنجاح',
        ], 200);
    }


    //show all bookings
    public function index()
    {
        // السماح فقط لـ Admin

        $bookings = Booking::with(['user', 'car'])->get();
        return response()->json($bookings, 200);
    }

    public function create(Request $request)
    {
        // تحقق إذا كان المستخدم لديه صلاحية Admin
        if (!auth()->user()->roles->contains('name', 'Admin')) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        // التحقق من البيانات المرسلة
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string',
            'status' => 'nullable|string|in:pending,confirmed,cancelled',
        ]);

        try {
            // استخدام strtotime لتحويل التاريخ بشكل صحيح إلى التنسيق المطلوب
            $startDate = $this->formatDate($validated['start_date']);
            $endDate = $this->formatDate($validated['end_date']);

            // إضافة التواريخ المحولة إلى البيانات
            $validated['start_date'] = $startDate;
            $validated['end_date'] = $endDate;

            // إنشاء الحجز
            $booking = Booking::create($validated);

            // رد نجاح مع تفاصيل الحجز
            return response()->json([
                'message' => 'تم إنشاء الحجز بنجاح',
                'booking' => $booking,
            ], 201);
        } catch (\Exception $e) {
            // في حالة حدوث خطأ أثناء عملية التحويل أو إنشاء الحجز
            return response()->json([
                'message' => 'حدث خطأ في الحجز: ' . $e->getMessage(),
                'error' => $e->getTrace(),
            ], 500);
        }
    }

    // دالة للتحقق من تنسيق التاريخ بدون استخدام Carbon أو DateTime
    private function formatDate($date)
    {
        // تحويل التاريخ باستخدام strtotime للحصول على الوقت بصيغة UNIX timestamp
        $timestamp = strtotime($date);

        // إذا كانت قيمة التاريخ صالحة (timestamp غير فارغ)
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);  // تنسيق التاريخ بالشكل المطلوب Y-m-d
        }

        // إذا كان التاريخ غير صالح، رمي استثناء
        throw new \Exception("التاريخ غير صالح: " . $date);
    }





    public function show($id)
    {
        $booking = Booking::with(['user', 'car'])->find($id);
        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }
        return response()->json($booking, 200);
    }

    public function update(Request $request, $id)
    {
        // تحقق إذا كان المستخدم لديه صلاحية Admin
        if (!auth()->user()->roles->contains('name', 'Admin')) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        // العثور على الحجز بناءً على الـ ID
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }

        // التحقق من البيانات المرسلة
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string',
            'status' => 'nullable|string|in:pending,confirmed,cancelled',
        ]);

        try {
            // تحويل التواريخ
            $startDate = $this->formatDate($validated['start_date']);
            $endDate = $this->formatDate($validated['end_date']);

            // تعديل الحجز بالتواريخ الجديدة
            $booking->update([
                'user_id' => $validated['user_id'],
                'car_id' => $validated['car_id'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'location' => $validated['location'],
                'status' => $validated['status'] ?? $booking->status,
            ]);

            // رد النجاح
            return response()->json([
                'message' => 'تم تعديل الحجز بنجاح',
                'booking' => $booking,
            ], 200);
        } catch (\Exception $e) {
            // التعامل مع الأخطاء
            return response()->json([
                'message' => 'حدث خطأ أثناء التعديل: ' . $e->getMessage(),
                'error' => $e->getTrace(),
            ], 500);
        }
    }



    // delete booking
    public function cancel($id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود'], 404);
        }

        // إشعار إلغاء الحجز
        // $user = User::find($booking->user_id);
        // Notification::send($user, new BookingNotification($booking, 'cancelled'));
        $booking->delete();
        return response()->json(['message' => 'تم إلغاء الحجز بنجاح'], 200);
    }
}
