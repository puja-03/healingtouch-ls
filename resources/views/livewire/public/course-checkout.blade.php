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
        <script>
            (function () {
                // Safe initializer for Razorpay checkout that works with Livewire updates.
                function setupRazorpay() {
                    var btn = document.getElementById('rzp-button');
                    if (!btn) return;

                    try {
                        var options = {
                            key: '{{ $razorpayKey }}',
                            order_id: '{{ $orderId }}',
                            handler: function (response) {
                                // Use Livewire's @this to call the server method with payment data
                                @this.handlePaymentSuccess({
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_signature: response.razorpay_signature
                                });
                            },
                            prefill: {
                                name: '{{ auth()->user()->name ?? "" }}',
                                email: '{{ auth()->user()->email ?? "" }}'
                            },
                            theme: { color: '#ec4899' }
                        };

                        // If Razorpay is available, create instance; otherwise it will be created on script load
                        if (typeof Razorpay !== 'undefined') {
                            var rzp = new Razorpay(options);
                            btn.onclick = function (e) {
                                e.preventDefault();
                                rzp.open();
                            };
                        }
                    } catch (err) {
                        console.error('Razorpay setup error:', err);
                    }
                }

                // Load checkout.js if not already present, then setup
                function ensureScriptAndSetup() {
                    if (typeof Razorpay !== 'undefined') {
                        setupRazorpay();
                        return;
                    }

                    var scriptId = 'razorpay-checkout-js';
                    if (document.getElementById(scriptId)) {
                        // script already added but Razorpay may not be ready yet
                        document.getElementById(scriptId).addEventListener('load', setupRazorpay);
                        return;
                    }

                    var s = document.createElement('script');
                    s.id = scriptId;
                    s.src = 'https://checkout.razorpay.com/v1/checkout.js';
                    s.onload = setupRazorpay;
                    s.onerror = function () { console.error('Failed to load Razorpay checkout.js'); };
                    document.head.appendChild(s);
                }

                // Initialize now
                ensureScriptAndSetup();

                // Re-initialize after Livewire updates (works in Livewire v2/v3)
                document.addEventListener('livewire:load', function () {
                    if (window.Livewire && typeof Livewire.hook === 'function') {
                        Livewire.hook('message.processed', function () {
                            ensureScriptAndSetup();
                        });
                    } else {
                        document.addEventListener('livewire:update', function () {
                            ensureScriptAndSetup();
                        });
                    }
                });
            })();
        </script>
    @endif
</div>
