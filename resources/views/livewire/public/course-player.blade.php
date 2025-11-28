<div class="min-h-screen">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <!-- Main Content -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Video Player -->
                <div class="bg-black aspect-video flex items-center justify-center">
                    @if($selectedTopic && $videoUrl)
               <video id="courseVideo" 
                   class="video-js w-full h-full" 
                   controls 
                   crossorigin="anonymous"
                   data-setup='{"controls": true, "autoplay": false, "preload": "auto"}'>
                            <source src="{{ $videoUrl }}" type="video/mp4">
                            Your browser does not support HTML5 video.
                        </video>
                    @else
                        <div class="text-white text-center">
                            <i class="fas fa-video text-6xl mb-4"></i>
                            <p class="text-lg">Select a topic to play video</p>
                        </div>
                    @endif
                </div>

                <!-- Video Info -->
                @if($selectedTopic)
                <div class="p-6">
                    <h2 class="text-2xl font-bold mb-2">{{ $selectedTopic->topic_title }}</h2>
                    <p class="text-gray-600 mb-4">{{ $selectedTopic->content }}</p>
                    
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-pink-100 text-pink-800 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-book-open mr-1"></i>{{ $selectedTopic->chapter->chapter_title }}
                        </span>
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-video mr-1"></i>Video Topic
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar - Chapters & Topics -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden sticky top-6">
                <div class="bg-gradient-to-r from-pink-600 to-pink-700 p-4">
                    <h3 class="text-white font-bold text-lg">
                        <i class="fas fa-list mr-2"></i>{{ $course->title }}
                    </h3>
                    <p class="text-pink-100 text-xs mt-1">
                        {{ $course->chapters->count() }} chapters • 
                        {{ $course->chapters->sum(fn($c) => $c->topics->count()) }} topics
                    </p>
                </div>

                <!-- Chapters List -->
                <div class="max-h-96 overflow-y-auto">
                    @forelse($course->chapters as $chapter)
                        <div class="border-b">
                            <!-- Chapter Header -->
                            <button 
                                wire:click="toggleChapter({{ $chapter->id }})"
                                class="w-full flex items-center justify-between p-4 hover:bg-gray-50 transition">
                                <div class="flex items-start gap-2 flex-1">
                                    <i class="fas fa-chevron-{{ $expandedChapter === $chapter->id ? 'down' : 'right' }} text-pink-600 mt-1"></i>
                                    <div class="text-left">
                                        <p class="font-semibold text-gray-800">{{ $chapter->chapter_title }}</p>
                                        <p class="text-xs text-gray-500">{{ $chapter->topics->count() }} topics</p>
                                    </div>
                                </div>
                            </button>

                            <!-- Topics List (Expandable) -->
                            @if($expandedChapter === $chapter->id)
                                <div class="bg-gray-50 border-t">
                                    @forelse($chapter->topics as $topic)
                                        <button
                                            wire:click="selectTopic({{ $topic->id }})"
                                            class="w-full flex items-center gap-3 p-3 ml-6 text-left hover:bg-gray-200 transition {{ $selectedTopic && $selectedTopic->id === $topic->id ? 'bg-pink-100 border-l-4 border-pink-600' : '' }}">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-700 truncate">
                                                    <i class="fas fa-play-circle text-pink-600 mr-2"></i>{{ $topic->topic_title }}
                                                </p>
                                            </div>
                                            @if($selectedTopic && $selectedTopic->id === $topic->id)
                                                <i class="fas fa-check-circle text-pink-600"></i>
                                            @endif
                                        </button>
                                    @empty
                                        <p class="p-3 ml-6 text-xs text-gray-500">No topics yet</p>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p>No chapters available</p>
                        </div>
                    @endforelse
                </div>

                <!-- Back Button -->
                <div class="border-t p-4">
                    <a href="{{ route('user.courses') }}" 
                       class="block text-center py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Courses
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Video.js CDN -->
    <link href="https://vjs.zencdn.net/7.20.3/video-js.min.css" rel="stylesheet" />
    <script src="https://vjs.zencdn.net/7.20.3/video.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var video = document.getElementById('courseVideo');
            if (video && video.classList.contains('video-js')) {
                videojs(video);
            }
        });

        // Re-initialize video.js after Livewire updates
        document.addEventListener('livewire:navigated', function() {
            var video = document.getElementById('courseVideo');
            if (video) {
                if (videojs.getPlayer(video)) {
                    videojs.getPlayer(video).dispose();
                }
                setTimeout(() => {
                    videojs(video);
                }, 100);
            }
        });
    </script>
</div>
