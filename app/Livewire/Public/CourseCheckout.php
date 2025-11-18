<?php

namespace App\Livewire\Public;

use App\Models\Course;
use App\Models\Payment;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Razorpay\Api\Api;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Topic;
use App\Models\Chapters;

#[Layout('components.layouts.public')]
#[Title('Course Checkout')]                 
class CourseCheckout extends Component
{
    public $courseId;
    public $course;
    public $orderId = null;
    public $showPayment = false;

    public function mount($courseId)
    {
        $this->courseId = $courseId;
        $this->course = Course::where('is_published', true)
            ->findOrFail($courseId);
    }

    public function initiateCheckout()
    {
        if (!auth()->check()) {
            return redirect()->guest(route('login'));
        }

        // Check if already enrolled
        $existing = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $this->courseId)
            ->where('status', 'completed')
            ->first();

        if ($existing) {
            session()->flash('info', 'You are already enrolled in this course.');
            return;
        }

        try {
            $api = new Api(
                config('razorpay.razorpay.key'),
                config('razorpay.razorpay.secret')
            );

            $orderData = [
                'amount' => (int)($this->course->price * 100), // amount in paise
                'currency' => 'INR',
                'receipt' => 'receipt#' . $this->courseId . '_' . auth()->id(),
                'notes' => [
                    'user_id' => auth()->id(),
                    'course_id' => $this->courseId,
                    'course_title' => $this->course->title,
                ]
            ];

            $razorpayOrder = $api->order->create($orderData);

            // Create payment record
            $payment = Payment::create([
                'user_id' => auth()->id(),
                'course_id' => $this->courseId,
                'razorpay_order_id' => $razorpayOrder['id'],
                'amount' => $this->course->price,
                'currency' => 'INR',
                'status' => 'created',
            ]);

            $this->orderId = $razorpayOrder['id'];
            $this->showPayment = true;

            session()->flash('message', 'Ready for payment. Complete your purchase below.');
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage());
            session()->flash('error', 'Failed to initiate payment. Please try again.');
        }
    }

    public function handlePaymentSuccess($paymentData)
    {
        try {
            // Verify payment signature
            $api = new Api(
                config('razorpay.razorpay.key'),
                config('razorpay.razorpay.secret')
            );

            $attributes = [
                'razorpay_order_id' => $paymentData['razorpay_order_id'],
                'razorpay_payment_id' => $paymentData['razorpay_payment_id'],
                'razorpay_signature' => $paymentData['razorpay_signature']
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // Update payment record
            Payment::where('razorpay_order_id', $paymentData['razorpay_order_id'])
                ->update([
                    'razorpay_payment_id' => $paymentData['razorpay_payment_id'],
                    'razorpay_signature' => $paymentData['razorpay_signature'],
                    'status' => 'captured',
                ]);

            // Create enrollment
            Enrollment::create([
                'user_id' => auth()->id(),
                'course_id' => $this->courseId,
                'payment_id' => Payment::where('razorpay_payment_id', $paymentData['razorpay_payment_id'])->first()->id,
                'razorpay_order_id' => $paymentData['razorpay_order_id'],
                'razorpay_payment_id' => $paymentData['razorpay_payment_id'],
                'razorpay_signature' => $paymentData['razorpay_signature'],
                'amount' => $this->course->price,
                'currency' => 'INR',
                'status' => 'completed',
                'enrolled_at' => now(),
            ]);

            session()->flash('success', 'Payment successful! You are now enrolled in the course.');
            $this->showPayment = false;
            $this->orderId = null;

            // After purchase, send the user to their purchased courses page
            return redirect()->route('user.courses');
        } catch (\Exception $e) {
            Log::error('Payment verification failed: ' . $e->getMessage());
            session()->flash('error', 'Payment verification failed. Contact support.');
        }
    }

    public function render()
    {
        return view('livewire.public.course-checkout', [
            'razorpayKey' => config('razorpay.razorpay.key'),
        ]);
    }
}
