<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - {{ $course->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Complete Payment</h1>
            <p class="text-gray-600 mt-2">{{ $course->title }}</p>
            <p class="text-3xl font-bold text-pink-600 mt-4">₹{{ number_format($course->price, 0) }}</p>
        </div>

        <button id="pay-button" 
                class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
            Pay Now
        </button>

        <div id="error-message" class="hidden mt-4 p-3 bg-red-100 text-red-700 rounded"></div>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.getElementById('pay-button').addEventListener('click', function() {
            const button = this;
            button.disabled = true;
            button.textContent = 'Processing...';

            // Create order first
            fetch('/create-order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    course_id: {{ $course->id }}
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Razorpay options
                    const options = {
                        key: data.key,
                        amount: data.amount,
                        currency: 'INR',
                        order_id: data.order_id,
                        name: data.course_name,
                        description: 'Course Enrollment',
                        handler: function(response) {
                            // Submit form to success endpoint
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route("payment.success") }}';
                            
                            const csrf = document.createElement('input');
                            csrf.name = '_token';
                            csrf.value = '{{ csrf_token() }}';
                            form.appendChild(csrf);
                            
                            const orderId = document.createElement('input');
                            orderId.name = 'razorpay_order_id';
                            orderId.value = response.razorpay_order_id;
                            form.appendChild(orderId);
                            
                            const paymentId = document.createElement('input');
                            paymentId.name = 'razorpay_payment_id';
                            paymentId.value = response.razorpay_payment_id;
                            form.appendChild(paymentId);
                            
                            const signature = document.createElement('input');
                            signature.name = 'razorpay_signature';
                            signature.value = response.razorpay_signature;
                            form.appendChild(signature);
                            
                            document.body.appendChild(form);
                            form.submit();
                        },
                        prefill: {
                            name: data.user_name,
                            email: data.user_email
                        },
                        theme: {
                            color: '#EC4899'
                        }
                    };

                    const rzp = new Razorpay(options);
                    rzp.open();
                    
                    rzp.on('payment.failed', function(response) {
                        document.getElementById('error-message').textContent = 
                            'Payment failed: ' + response.error.description;
                        document.getElementById('error-message').classList.remove('hidden');
                        button.disabled = false;
                        button.textContent = 'Pay Now';
                    });

                } else {
                    throw new Error(data.error);
                }
            })
            .catch(error => {
                document.getElementById('error-message').textContent = 
                    'Error: ' + error.message;
                document.getElementById('error-message').classList.remove('hidden');
                button.disabled = false;
                button.textContent = 'Pay Now';
            });
        });
    </script>
</body>
</html>