<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Payment;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

class PaymentController
{
    public function showCheckout($courseParam)
    {
        // Accept Course model (route-model binding), slug or id
        if ($courseParam instanceof Course) {
            $course = $courseParam;
        } elseif (is_numeric($courseParam)) {
            $course = Course::findOrFail($courseParam);
        } else {
            $course = Course::where('slug', $courseParam)->firstOrFail();
        }

        return view('payment.checkout', compact('course'));
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $course = Course::find($request->course_id);
        $user = auth()->user();

        // Check if already enrolled
        $existing = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'completed')
            ->first();

        if ($existing) {
            // Redirect using the slug-based parameter expected by user.play-course
            return redirect()->route('user.play-course', ['course' => $course->slug]);
        }

        try {
            $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

            $order = $api->order->create([
                'amount' => $course->price * 100,
                'currency' => 'INR',
                'receipt' => 'rcpt_'.time(),
            ]);

            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'razorpay_order_id' => $order->id,
                'amount' => $course->price,
                'currency' => 'INR',
                'status' => 'created',
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'amount' => $course->price * 100,
                'key' => env('RAZORPAY_KEY_ID'),
                'course_name' => $course->title,
                'user_name' => $user->name,
                'user_email' => $user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Razorpay Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function handleSuccess(Request $request)
    {
        try {
            $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // Update payment
            $payment = Payment::where('razorpay_order_id', $request->razorpay_order_id)->first();
            if ($payment) {
                $payment->update([
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature,
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);

                // Create enrollment
                Enrollment::create([
                    'user_id' => auth()->id(),
                    'course_id' => $payment->course_id,
                    'payment_id' => $payment->id,
                    'status' => 'completed',
                    'enrolled_at' => now(),
                ]);

                    // Redirect using the slug-based route parameter expected by user.play-course
                    $course = Course::find($payment->course_id);
                    return redirect()->route('user.play-course', ['course' => $course->slug])
                        ->with('success', 'Payment successful! You are now enrolled.');
            }

        } catch (\Exception $e) {
            Log::error('Payment verification failed: ' . $e->getMessage());
            return redirect()->route('courses.show', $request->course_id ?? 1)
                ->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }
}
