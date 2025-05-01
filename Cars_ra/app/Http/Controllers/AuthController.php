<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // signup for new user
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'license_number' => 'required|string|max:50',
            'license_image' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'البيانات المدخلة غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // إضافة role مباشرة في جدول users
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'license_number' => $request->license_number,
                'license_image' => $request->license_image,

            ]);

            return response()->json([
                'message' => 'تم تسجيل المستخدم بنجاح',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء تسجيل المستخدم.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    // login for user
    public function login(Request $request)
    {
        // التحقق من صحة المدخلات
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'البيانات المدخلة غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        // البحث عن المستخدم باستخدام البريد الإلكتروني
        $user = User::where('email', $request->email)->first();

        // التحقق من كلمة المرور
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
            ], 401);
        }

        // إنشاء التوكن
        $token = $user->createToken('auth_token')->plainTextToken;

        // إرسال التوكن والدور مع الاستجابة
        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'token' => $token,
            'role' => $user->role,  // إرسال الدور
            'user' => $user,        // إرسال بيانات المستخدم (اختياري)
        ], 200);
    }




    public function logout(Request $request)
    {
        try {
            // chek tokens
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'لم يتم العثور على المستخدم'
                ], 401); // Unauthorized
            }

            // delete tokens
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json([
                'message' => 'تم تسجيل الخروج بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء محاولة تسجيل الخروج',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    //update informtion user personal
    public function updateUser(Request $request, $user_id)
    {
        // السماح فقط للـ Admin
        if (!auth()->user()->roles->contains('name', 'Admin')) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $user_id,
            'phone'          => 'required|string|max:15',
            'address'        => 'required|string|max:255',
            'license_number' => 'required|string|max:50',
            'license_image'  => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'role'           => 'required|in:Admin,Employee',
            'password'       => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'البيانات المدخلة غير صحيحة',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // البحث عن المستخدم
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['message' => 'المستخدم غير موجود'], 404);
        }

        // تجميع بيانات التحديث
        $data = $request->only([
            'name',
            'email',
            'phone',
            'address',
            'license_number',
        ]);

        // إذا تم رفع صورة رخصة جديدة
        if ($request->hasFile('license_image')) {
            $data['license_image'] = $request
                ->file('license_image')
                ->store('licenses', 'public');
        }

        // إذا تم إدخال كلمة سر جديدة
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // تحديث البيانات في جدول users
        $user->update($data);

        // تحديث الدور في جدول users مباشرة
        $user->role = $request->role;

        // حفظ الدور الجديد في جدول users
        $user->save();

        // تعيين الدور باستخدام Spatie في جدول الـ pivot
        $user->syncRoles([$request->role]);

        // إرجاع الاستجابة مع بيانات المستخدم والدور الجديد
        return response()->json([
            'message' => 'تم تعديل بيانات المستخدم بنجاح',
            'user'    => $user->fresh(),
            'role'    => $user->roles()->pluck('name')->first(),
        ], 200);
    }


    //delet account user
    public function deleteUser($user_id)
    {
        // السماح فقط لـ Admin
        if (!auth()->user()->roles->contains('name', 'Admin')) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        } else {
            // Search for user
            $user = User::find($user_id);

            //verify the user's presence
            if (!$user) {
                return response()->json([
                    'message' => 'المستخدم غير موجود',
                ], 404); // Status code 404: Not Found
            }


            $user->delete();

            return response()->json([
                'message' => 'تم حذف المستخدم بنجاح',
            ], 200); // Status code 200: OK
        }
    }

    ////////////////// اضافة موظف //////////////////

    public function storeEmployee(Request $request)
    {
        // السماح فقط لـ Admin
        if (!auth()->user()->roles->contains('name', 'Admin')) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        } else {

            // التحقق من صحة البيانات
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'phone' => 'nullable|string|max:15',
                'address' => 'nullable|string|max:255',
                'license_number' => 'nullable|string|max:50',
                'license_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // تعديل هنا لقبول الصور
                'role' => 'required|in:Admin,Employee',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'البيانات غير صحيحة',
                    'errors' => $validator->errors(),
                ], 422);
            }

            try {
                // إنشاء المستخدم
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->phone = $request->phone;
                $user->address = $request->address;
                $user->license_number = $request->license_number;

                // معالجة صورة الرخصة إذا تم رفعها
                if ($request->hasFile('license_image')) {
                    $image = $request->file('license_image');
                    $imagePath = $image->store('licenses', 'public');
                    $user->license_image = $imagePath;
                }

                // تخزين الدور في جدول users
                $user->role = $request->role;

                // حفظ المستخدم
                $user->save();

                // تعيين الدور باستخدام Spatie
                $user->assignRole($request->role);

                return response()->json([
                    'message' => 'تم إضافة المستخدم بنجاح',
                    'user' => $user,
                    'role' => $request->role,
                ], 201);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'حدث خطأ أثناء إضافة المستخدم',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
    }



    public function storeUser(Request $request)
    {
        //  صلاحية Employee فقط
        if (!auth()->user()->roles->contains('name', 'Employee')) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        } else {

            $validator = Validator::make($request->all(), [
                'name'           => 'required|string|max:255',
                'email'          => 'required|email|unique:users,email',
                'password'       => 'required|string|min:6',
                'phone'          => 'nullable|string|max:15',
                'address'        => 'nullable|string|max:255',
                'license_number' => 'nullable|string|max:50',
                'license_image'  => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'البيانات المدخلة غير صحيحة',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // جمع بيانات المستخدم
            $data = $request->only([
                'name',
                'email',
                'phone',
                'address',
                'license_number'
            ]);

            //تشفير كلمة المرور
            $data['password'] = Hash::make($request->password);

            //  معالجة صورة الرخصة إذا رفعت
            if ($request->hasFile('license_image')) {
                $data['license_image'] = $request
                    ->file('license_image')
                    ->store('licenses', 'public');
            }

            //  إنشاء المستخدم
            $user = User::create($data);


            return response()->json([
                'message' => 'تم إضافة المستخدم بنجاح',
                'user'    => $user,
            ], 201);
        }
    }
}
