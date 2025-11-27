<div class="min-h-screen bg-gray-50">
    <div class="px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('home') }}" class="text-pink-600 hover:underline text-sm mb-4 inline-block">
                    <i class="fas fa-arrow-left mr-1"></i>Back to courses
                </a>
                <h1 class="text-4xl font-bold mb-2">{{ $course->title }}</h1>
                <p class="text-gray-600">By <span class="font-semibold">{{ $course->user->name }}</span></p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Course Description -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-2xl font-bold mb-4">About This Course</h2>
                        <p class="text-gray-700 leading-relaxed">{{ $course->description }}</p>
                    </div>

                    <!-- Course Stats -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-gradient-to-br from-pink-100 to-pink-50 rounded-lg p-4 text-center">
                            <p class="text-3xl font-bold text-pink-600">{{ $course->chapters->count() }}</p>
                            <p class="text-gray-600 text-sm">Chapters</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg p-4 text-center">
                            <p class="text-3xl font-bold text-blue-600">
                                {{ $course->chapters->sum(fn($c) => $c->topics->count()) }}</p>
                            <p class="text-gray-600 text-sm">Topics</p>
                        </div>
                        <div class="bg-gradient-to-br from-green-100 to-green-50 rounded-lg p-4 text-center">
                            <p class="text-3xl font-bold text-green-600">{{ $course->is_published ? '✓' : '✗' }}</p>
                            <p class="text-gray-600 text-sm">Status</p>
                        </div>
                    </div>

                    <!-- Chapters List -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-2xl font-bold mb-4">Course Content</h2>
                        <div class="space-y-3">
                            @forelse($course->chapters as $chapter)
                                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-800">{{ $chapter->chapter_title }}</h3>
                                            <p class="text-sm text-gray-500">
                                                <i class="fas fa-video mr-1"></i>{{ $chapter->topics->count() }} topics
                                            </p>
                                        </div>
                                        <i class="fas fa-chevron-right text-gray-400"></i>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 py-4">No chapters available yet</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Pricing & CTA -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                        <!-- Price -->
                        <div class="mb-6">
                            <p class="text-gray-600 text-sm mb-2">Course Price</p>
                            <p class="text-4xl font-bold text-pink-600">₹{{ number_format($course->price, 0) }}</p>
                        </div>

                        <!-- CTA Button -->
                        <div class="mb-6">
                            {{-- @auth
                                <livewire:public.course-checkout :course-id="$course->id" />
                            @else
                                <a href="{{ route('login', ['intended' => request()->url()]) }}" 
                                   class="block text-center px-4 py-3 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition font-semibold">
                                    <i class="fas fa-lock mr-2"></i>Login to Enroll
                                </a>
                           @endauth --}}
                            @auth
                                @php
                                    // Consider any enrollment record as enrolled (ignore enrollment status)
                                    $isEnrolled = Enrollment::where('user_id', auth()->id())
                                        ->where('course_id', $course->id)
                                        ->exists();
                                @endphp

                                @if ($isEnrolled)
                                    <a href="{{ route('user.play-course', ['course' => $course->slug]) }}"
                                        class="block text-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                                        Start Learning
                                    </a>
                                @else
                                    <a href="{{ route('payment.checkout', ['course' => $course->slug]) }}"
                                        class="block text-center px-4 py-3 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition font-semibold">
                                        Enroll Now - ₹{{ number_format($course->price, 0) }}
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}"
                                    class="block text-center px-4 py-3 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition font-semibold">
                                    Login to Enroll
                                </a>
                            @endauth

                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded p-4 text-sm text-blue-800">
                            <i class="fas fa-shield-alt mr-2"></i>
                            <span>Secure payment via Razorpay</span>
                        </div>

                        <!-- Features -->
                        <div class="mt-6 pt-6 border-t space-y-3">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                <span class="text-sm text-gray-700">Lifetime access</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                <span class="text-sm text-gray-700">Watch offline</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                <span class="text-sm text-gray-700">Learn at your pace</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
