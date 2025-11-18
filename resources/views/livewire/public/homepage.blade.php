<div class="min-h-screen">
    {{-- Search Bar --}}
    <div class=" px-4 py-8">
        <input type="text" 
               wire:model.debounce.300ms="search" 
               placeholder="Search courses by title or description..."
               class="w-full rounded-lg border-gray-300 px-4 py-3 shadow-sm focus:ring-pink-500 focus:border-pink-500">
    </div>

    {{-- Courses Grid --}}
    <div class="px-4 pb-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($courses as $course)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden">
                    <div class="h-48 bg-gradient-to-r from-pink-400 to-pink-600 flex items-center justify-center">
                        <span class="text-white text-4xl">📚</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $course->title }}</h3>
                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($course->description, 80) }}</p>
                        <p class="text-xs text-gray-500 mb-4">By {{ $course->user->name }}</p>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-pink-600">₹{{ number_format($course->price, 0) }}</span>
                            <a href="{{ route('courses.show', $course->slug) }}" 
                                    class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition text-sm">
                                View
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">No courses found. Try a different search.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-12">
            {{ $courses->links() }}
        </div>
    </div>
</div>
