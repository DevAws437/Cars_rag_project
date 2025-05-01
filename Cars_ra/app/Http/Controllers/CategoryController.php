<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    // عرض جميع الفئات
    public function index()
    {
        return response()->json(Category::all(), 200);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'الفئة غير موجودة'], 404);
        }

        return response()->json($category, 200);
    }


    // إضافة فئة جديدة
    public function store(Request $request)
    {
        // السماح فقط لـ Admin أو Employee
        if (!auth()->user()->roles->pluck('name')->contains(function ($role) {
            return in_array($role, ['Admin', 'Employee']);
        })) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        } else {

            // التحقق من البيانات
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'nullable|string',
                'car_id' => 'required|exists:cars,id', //  تحقق من وجود السيارة
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // إنشاء الفئة
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'car_id' => $request->car_id, // إدخال car_id
            ]);

            return response()->json([
                'message' => 'تمت إضافة الفئة بنجاح',
                'category' => $category
            ], 201);
        }
    }


    // تعديل فئة
    public function update(Request $request, $id)
    {
        // السماح فقط لـ Admin أو Employee
        if (!auth()->user()->roles->pluck('name')->contains(function ($role) {
            return in_array($role, ['Admin', 'Employee']);
        })) {
            // في حالة عدم وجود صلاحية للمستخدم
            return response()->json(['message' => 'غير مصرح لك'], 403);
        } else {

            // العثور على الفئة باستخدام  ID
            $category = Category::find($id);

            if (!$category) {
                return response()->json(['message' => 'الفئة غير موجودة'], 404);
            }

            // التحقق من البيانات
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string',
                'description' => 'nullable|string',
                'car_id' => 'sometimes|exists:cars,id', // تحقق من وجود car_id في جدول cars
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // تحديث البيانات
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'car_id' => $request->car_id, // تحديث car_id إذا تم إرساله في الطلب
            ]);

            // رد النجاح
            return response()->json(['message' => 'تم تعديل الفئة بنجاح', 'category' => $category], 200);
        }
    }

    // حذف فئة
    public function destroy($id)
    {
        // السماح فقط لـ Admin
        if (!auth()->user()->roles->pluck('name')->contains(function ($role) {
            return in_array($role, ['Admin', 'Employee']);
        })) {
            // في حالة عدم وجود صلاحية للمستخدم
            return response()->json(['message' => 'غير مصرح لك'], 403);
        } else {
            $category = Category::find($id);

            if (!$category) {
                return response()->json(['message' => 'الفئة غير موجودة'], 404);
            }

            $category->delete();

            return response()->json(['message' => 'تم حذف الفئة بنجاح'], 200);
        }
    }
}
