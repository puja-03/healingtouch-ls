<div class="min-h-screen">
    <div class=" px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold">My Learning Dashboard</h1>
            <a href="/" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Browse More Courses
            </a>
        </div>
        {{-- Search --}}
        <div class="mb-8">
            <input type="text" 
                   wire:model.debounce.300ms="search" 
                   placeholder="Search your courses..."
                   class="w-full rounded-lg border-gray-300 px-4 py-3 shadow-sm focus:ring-pink-500 focus:border-pink-500">
        </div>
        {{-- Enrolled Courses --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($enrollments as $enrollment)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden">
                    <div class="h-40 bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center">
                        <span class="text-white text-4xl">📚</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $enrollment->course->title }}</h3>
                        
                        {{-- Progress Bar --}}
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-semibold text-gray-600">Progress</span>
                                <span class="text-xs font-semibold text-gray-600">0%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-pink-600 h-2 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Directly open the course player for the purchased course -->
                        <a href="{{ route('user.play-course', ['course' => $enrollment->course->slug]) }}"
                           class="w-full inline-block text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                            <i class="fas fa-play mr-1"></i>Continue Learning
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <p class="text-gray-500 text-lg mb-4">You haven't enrolled in any courses yet.</p>
                    <a href="/" class="px-6 py-3 bg-pink-600 text-white rounded-lg hover:bg-pink-700">
                        Explore Courses
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-12">
            {{ $enrollments->links() }}
        </div>
    </div>
</div>
