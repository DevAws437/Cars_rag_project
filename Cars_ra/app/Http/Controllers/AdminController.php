<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\User;
use Illuminate\Support\Facades\Validator;



class AdminController extends Controller
{
    //To appoint admins
    public function makeAdmin($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير موجود.'], 404);
        }

        $user->role = 'admin';
        $user->save();

        return response()->json(['message' => 'تم ترقية المستخدم إلى مسؤول بنجاح.']);
    }

    public function index()
    {
        $users = User::all();

        return response()->json([
            'message' => 'تم عرض جميع المستخدمين بنجاح',
            'users' => $users
        ], 200);
    }


    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير موجود'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'role' => 'nullable|string|in:user,admin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->all());

        return response()->json(['message' => 'تم تعديل المستخدم بنجاح', 'user' => $user], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير موجود'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'تم حذف المستخدم بنجاح'], 200);
    }

    public function show($id)
    {
        // البحث عن المستخدم بناءً على رقم الـ ID
        $user = User::find($id);

        // إذا لم يتم العثور على المستخدم
        if (!$user) {
            return response()->json([
                'message' => 'المستخدم غير موجود'
            ], 404);
        }

        // إذا تم العثور على المستخدم، قم بإرجاع البيانات
        return response()->json([
            'message' => 'تم العثور على المستخدم بنجاح',
            'user' => $user
        ], 200);
    }
}
