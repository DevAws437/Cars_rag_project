<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\User;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\NotificationController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Route for signup
Route::post('/signup', [AuthController::class, 'signup']);

// Route for login
Route::post('login', [AuthController::class, 'login']);

// Route for logout
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::post('/employees', [AuthController::class, 'storeEmployee']);
});

/////////////////// Admin ///////////////////


Route::middleware(['auth:sanctum', 'is_admin'])->post('/users/{id}/make-admin', [AdminController::class, 'makeAdmin']);


Route::get('/admin/users', [AdminController::class, 'index'])->middleware('auth:sanctum');

Route::put('/admin/users/{id}', [AdminController::class, 'update'])->middleware('auth:sanctum');

Route::delete('/admin/users/{id}', [AdminController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('/admin/users/{id}', [AdminController::class, 'show'])->middleware('auth:sanctum');



/////////////////// Users ///////////////////


//Route show all user

//Route show user by id
Route::get('/users/{id}', [AuthController::class, 'show'])->middleware('auth:sanctum');

//Route update user
Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::put('users/{user_id}', [AuthController::class, 'updateUser'])->middleware('auth:sanctum');
});
//Route delete user
Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::delete('users/{user_id}', [AuthController::class, 'deleteUser'])->middleware('auth:sanctum');
});

/////////////////// Cars ///////////////////


//Route show and filter cars
Route::get('/cars', [CarController::class, 'showall']);

Route::get('/cars', [CarController::class, 'index']);

//Route show cars by id
Route::get('/cars/{id}', [CarController::class, 'show']);

//Route add cars
Route::middleware(['auth:sanctum', 'role:Admin|Employee'])->group(function () {
    Route::post('/cars', [CarController::class, 'store']);
});

//Route update cars
Route::middleware(['auth:sanctum', 'role:Admin|Employee'])->group(function () {
    Route::put('/cars/{id}', [CarController::class, 'update']);
});

//Route Delete cars
Route::middleware(['auth:sanctum', 'role:Admin|Employee'])->group(function () {
    Route::delete('/cars/{id}', [CarController::class, 'destroy']);
});

/////////////////// Admin ///////////////////

Route::post('/bookings/{id}/confirm', [BookingController::class, 'confirmBooking']);


//Route add bookings
Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::post('/bookings', [BookingController::class, 'create']);
});

Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
});
//Route show all bookings
Route::get('/bookings', [BookingController::class, 'index']);

//Route show bookings by id
Route::get('/bookings/{id}', [BookingController::class, 'show']);

//Route Delete bookings
Route::delete('/bookings/{id}/cancel', action: [BookingController::class, 'cancel']);


/////////////////// Admin ///////////////////


//Route add payments
Route::post('/payments', [PaymentController::class, 'store']);

//Route show all payments
Route::get('/payments', [PaymentController::class, 'index']);

//Route show payments by id
Route::get('/payments/{id}', [PaymentController::class, 'show']);

//Route update payments
Route::put('/payments/{id}', [PaymentController::class, 'update']);

//Route Delete payments
Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);


/////////////////// Admin ///////////////////


// إضافة تقييم
Route::post('/reviews', [ReviewController::class, 'store']);


Route::get('/reviews/{id}', [ReviewController::class, 'show']);

// عرض جميع التقييمات
Route::get('/reviews', [ReviewController::class, 'index']);

// تعديل تقييم
Route::put('/reviews/{id}', [ReviewController::class, 'update']);

// حذف تقييم
Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->middleware('auth:sanctum');


/////////////////////// Categories ///////////////////


// عرض جميع التصنيفات
Route::get('/categories', [CategoryController::class, 'index']);

//  اضافة تصنيف
Route::middleware(['auth:sanctum', 'role:Admin|Employee'])->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']);
});

// تعديل تصنيف
Route::middleware(['auth:sanctum', 'role:Admin|Employee'])->group(function () {
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
});

// حذف تصنيف
Route::middleware(['auth:sanctum', 'role:Admin|Employee'])->group(function () {
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});

// عرض تصنيف بالمعرف
Route::get('/categories/{id}', [CategoryController::class, 'show']);


////////////////////////// Discounts /////////////////////


// عرض الخصومات
Route::get('/discount', [DiscountController::class, 'index']);

//اضافة الخصومات
Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::post('/discount', [DiscountController::class, 'store']);
});

// تعديل الخصومات
Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::put('/discount/{id}', [DiscountController::class, 'update']);
});

// حذف الخصومات
Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::delete('/discount/{id}', [DiscountController::class, 'destroy']);
});

// عرض خصومة بالمعرف
Route::get('/discount/{id}', [DiscountController::class, 'show']);

/////////////////// Notifications ////////////////////


// Send notification
Route::middleware('auth:sanctum')->
post('/notifications/mark-as-read', [NotificationController::class, 'markAllAsRead']);
Route::middleware('auth:sanctum')->
get('/notifications', [NotificationController::class, 'index']);

///////////////// Add employee ////////////////////

//Route add employees
Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::post('/add-employee', [AuthController::class, 'storeEmployee']);
});

//////////////////// Add a user by the employee /////////////////

Route::middleware(['auth:sanctum', 'role:Employee'])->group(function () {
    Route::post('/add-user', [AuthController::class, 'storeUser']);
});
