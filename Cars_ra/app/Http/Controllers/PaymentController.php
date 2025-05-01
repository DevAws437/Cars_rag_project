<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class PaymentController extends Controller
{
    // show all payments
    public function index()
    {
        $payments = Payment::with('booking')->get();
        return response()->json($payments, 200);
    }

    // add new payments amd store it
    public function store(Request $request)
    {
        //verify the input
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric',
            'status' => 'required|string',
            'invoice' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'البيانات المدخلة غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        $payment = Payment::create([
            'booking_id' => $request->booking_id,
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'status' => $request->status,
            'invoice' => $request->invoice,
        ]);

        //  Usually responds with status code 201 (Created)
        return response()->json([
            'message' => 'تم إضافة الدفعة بنجاح',
            'payment' => $payment,
        ], 201);
    }


    // show payments by id
    public function show($id)
    {
        $payment = Payment::with('booking')->find($id);
        if (!$payment) {
            return response()->json(['message' => 'الدفعة غير موجودة'], 404);
        }
        return response()->json($payment, 200);
    }

    // update information payments
    public function update(Request $request, $id)
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['message' => 'الدفعة غير موجودة'], 404);
        }
        //verify the input
        $validated = $request->validate([
            'payment_method' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:pending,completed,failed',
            'invoice' => 'nullable|string',
        ]);

        $payment->update($validated);
        return response()->json([
            'message' => 'تم تحديث الدفعة بنجاح',
            'payment' => $payment,
        ], 200);
    }

    // delete payments
    public function destroy($id)
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['message' => 'الدفعة غير موجودة'], 404);
        }

        $payment->delete();
        return response()->json(['message' => 'تم حذف الدفعة بنجاح'], 200);
    }
}
