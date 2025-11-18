<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold">My Learning Dashboard</h1>
            <a href="/" class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700">
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
                    <div class="h-40 bg-gradient-to-r from-pink-400 to-pink-600 flex items-center justify-center">
                        <span class="text-white text-4xl">📚</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $enrollment->course->title }}</h3>
                        <p class="text-xs text-gray-500 mb-4">Enrolled {{ $enrollment->enrolled_at->format('M d, Y') }}</p>
                        
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

                        <button wire:click="selectCourse({{ $enrollment->id }})" 
                                class="w-full px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition text-sm">
                            Continue Learning
                        </button>
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

    {{-- Course Content Modal --}}
    @if($selectedCourse)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b p-6 flex items-center justify-between">
                    <h2 class="text-2xl font-bold">{{ $selectedCourse->course->title }}</h2>
                    <button wire:click="closeCourseDetail" class="text-gray-500 hover:text-gray-700 text-2xl">✕</button>
                </div>

                <div class="p-6">
                    {{-- Chapters and Topics --}}
                    <div class="space-y-4">
                        @foreach($selectedCourse->course->chapters as $chapter)
                            <div class="border rounded-lg">
                                <div class="bg-gray-50 p-4 font-semibold text-gray-900">
                                    {{ $chapter->chapter_title }}
                                </div>
                                <div class="p-4 space-y-2">
                                    @foreach($chapter->topics as $topic)
                                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded">
                                            <span class="text-pink-600">▶️</span>
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">{{ $topic->topic_title }}</p>
                                                <p class="text-xs text-gray-500">
                                                    @if($topic->video_url)
                                                        <a href="{{ $topic->video_url }}" target="_blank" class="text-blue-600 hover:underline">Watch Video</a>
                                                    @else
                                                        No video available
                                                    @endif
                                                </p>
                                            </div>
                                            <input type="checkbox" class="rounded border-gray-300 text-pink-600">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
