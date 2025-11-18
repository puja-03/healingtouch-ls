<div>
    @if(!$showPayment)
        <button wire:click="initiateCheckout" 
                class="px-6 py-3 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition">
            Enroll Now
        </button>
    @else
        <div class="space-y-4">
            <p class="text-sm text-gray-600">Processing payment...</p>
            <button id="rzp-button" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition w-full">
                Complete Payment
            </button>
        </div>
    @endif

    @if($showPayment && $orderId)
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var options = {
                    key: '{{ $razorpayKey }}',
                    order_id: '{{ $orderId }}',
                    handler: function (response) {
                        Livewire.emit('paymentSuccess', {
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_signature: response.razorpay_signature
                        });
                    },
                    prefill: {
                        name: '{{ auth()->user()->name ?? "" }}',
                        email: '{{ auth()->user()->email ?? "" }}'
                    },
                    theme: {
                        color: '#ec4899'
                    }
                };

                var rzp = new Razorpay(options);
                
                document.getElementById('rzp-button').onclick = function(e) {
                    e.preventDefault();
                    rzp.open();
                }
            });
        </script>
    @endif
</div>
