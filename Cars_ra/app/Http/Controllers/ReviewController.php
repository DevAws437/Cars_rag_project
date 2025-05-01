<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;  // تأكد من إضافة هذا السطر

class ReviewController extends Controller
{

    // دالة عرض التقييمات
    public function index()
    {
        $reviews = Review::all();
        return response()->json([
            'reviews' => $reviews
        ], 200);
    }

    // دالة إضافة تقييم
    public function store(Request $request)
    {
        // سجل البيانات التي تم إرسالها
        Log::info('إضافة تقييم جديد:', $request->all());

        // تحقق من المدخلات
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'car_id' => 'required|exists:cars,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'البيانات المدخلة غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        // إنشاء المراجعة الجديدة
        $review = Review::create([
            'user_id' => $request->user_id,
            'car_id' => $request->car_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // سجل المراجعة التي تم إضافتها
        Log::info('تم إضافة المراجعة بنجاح:', $review->toArray());

        return response()->json([
            'message' => 'تم إضافة المراجعة بنجاح',
            'review' => $review,
        ], 201); // استخدم 201 Created هنا
    }


    public function show($id)
    {
        $reviews = Review::with('reviews')->find($id);
        if (!$reviews) {
            return response()->json(['message' => 'الدفعة غير موجودة'], 404);
        }
        return response()->json($reviews, 200);
    }

    // دالة تحديث التقييم
    public function update(Request $request, $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'التقييم غير موجود'], 404);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $review->update($request->all());

        return response()->json(['message' => 'تم تعديل التقييم بنجاح', 'review' => $review], 200);
    }

    // دالة حذف التقييم
    // public function destroy($id)
    // {
    //     // العثور على التقييم المطلوب
    //     $review = Review::find($id);

    //     if (!$review) {
    //         return response()->json([
    //             'message' => 'لم يتم العثور على التقييم المطلوب.'
    //         ], 404);
    //     }

    //     // حذف التقييم
    //     $review->delete();

    //     return response()->json([
    //         'message' => 'تم حذف التقييم بنجاح'
    //     ], 200);
    // }


    public function destroy($id)
    {
        // العثور على التقييم
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'التقييم غير موجود.'], 404);
        }

        // التحقق من أن المستخدم الحالي هو صاحب التقييم
        if (auth()->id() !== $review->user_id) {
            return response()->json(['message' => 'غير مصرح بحذف هذا التقييم.'], 403);
        }

        // حذف التقييم
        $review->delete();

        return response()->json(['message' => 'تم حذف التقييم بنجاح.'], 200);
    }
}
