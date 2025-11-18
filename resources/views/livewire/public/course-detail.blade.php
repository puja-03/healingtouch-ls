<div class="min-h-screen">
    <div class="px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-start justify-between gap-6">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold mb-2">{{ $course->title }}</h1>
                    <p class="text-sm text-gray-600 mb-4">By {{ $course->user->name }}</p>
                    <p class="text-gray-700 mb-6">{{ $course->description }}</p>

                    <div class="grid grid-cols-3 gap-4 text-center mb-6">
                        <div>
                            <p class="text-2xl font-bold text-pink-600">{{ $course->chapters->count() }}</p>
                            <p class="text-xs text-gray-600">Chapters</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-pink-600">{{ $course->chapters->sum(fn($c) => $c->topics->count()) }}</p>
                            <p class="text-xs text-gray-600">Topics</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-pink-600">{{ $course->is_published ? '✓' : '✗' }}</p>
                            <p class="text-xs text-gray-600">Published</p>
                        </div>
                    </div>

                    {{-- Chapters list (collapsed view) --}}
                    <div class="space-y-4">
                        @foreach($course->chapters as $chapter)
                            <div class="p-4 border rounded">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold">{{ $chapter->chapter_title }}</p>
                                        <p class="text-xs text-gray-500">{{ $chapter->topics->count() }} topics</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="w-80">
                    <div class="bg-white border rounded p-4 sticky top-24">
                        <p class="text-sm text-gray-600">Price</p>
                        <p class="text-3xl font-bold text-pink-600">₹{{ number_format($course->price, 0) }}</p>

                        <div class="mt-4">
                            @auth
                                <livewire:public.course-checkout :course-id="$course->id" />
                            @else
                                <a href="{{ route('login') }}" class="block text-center px-4 py-2 bg-pink-600 text-white rounded">Login to Enroll</a>
                            @endauth
                        </div>

                        <div class="mt-4 text-xs text-gray-500">
                            <p>Secure payment via Razorpay.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('user.browse') }}" class="text-sm text-pink-600 hover:underline">← Back to courses</a>
            </div>
        </div>
    </div>
</div>
