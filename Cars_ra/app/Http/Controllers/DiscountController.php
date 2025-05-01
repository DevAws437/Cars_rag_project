<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    // عرض جميع الحسومات
    public function index()
    {
        return response()->json(Discount::all(), 200);
    }

    // عرض حسم معين حسب  ID
    public function show($id)
    {
        $discount = Discount::find($id);

        if (!$discount) {
            return response()->json(['message' => 'الحسم غير موجود'], 404);
        }

        return response()->json($discount, 200);
    }

    // إضافة حسم جديد
    public function store(Request $request)
    {
        // السماح فقط لـ Admin
        if (!auth()->user()->roles->contains('name', 'Admin')) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        } else {
            $validator = Validator::make($request->all(), [
                'car_id' => 'required|exists:cars,id',
                'discount_value' => 'required|numeric|min:0',
                'discount_type' => 'required|in:daily,monthly',
                'expiration_date' => 'nullable|date|after_or_equal:today',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $discount = Discount::create($request->all());

            return response()->json(['message' => 'تمت إضافة الحسم بنجاح', 'discount' => $discount], 201);
        }
    }

    // تعديل حسم
    public function update(Request $request, $id)
    {
        // السماح فقط لـ Admin
        if (!auth()->user()->roles->contains('name', 'Admin')) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        } else {
            $discount = Discount::find($id);

            if (!$discount) {
                return response()->json(['message' => 'الحسم غير موجود'], 404);
            }

            $validator = Validator::make($request->all(), [
                'car_id' => 'sometimes|exists:cars,id',
                'discount_value' => 'sometimes|numeric|min:0',
                'discount_type' => 'sometimes|in:daily,monthly',
                'expiration_date' => 'nullable|date|after_or_equal:today',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $discount->update($request->all());

            return response()->json(['message' => 'تم تعديل الحسم بنجاح', 'discount' => $discount], 200);
        }
    }

    // حذف حسم
    public function destroy($id)
    {
        // السماح فقط لـ Admin
        if (!auth()->user()->roles->contains('name', 'Admin')) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        } else {
            $discount = Discount::find($id);

            if (!$discount) {
                return response()->json(['message' => 'الحسم غير موجود'], 404);
            }

            $discount->delete();

            return response()->json(['message' => 'تم حذف الحسم بنجاح'], 200);
        }
    }
}
