<div class="min-h-screen">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <!-- Main Content -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Video Player -->
                <div class="bg-black aspect-video flex items-center justify-center">
                    @if ($videoUrl)
                        <video id="courseVideo" class="w-full h-full" controls controlsList="nodownload"
                            style="background: #000;" oncontextmenu="return false;">
                            <source src="{{ $videoUrl }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <div class="text-white text-center">
                            <i class="fas fa-video text-6xl mb-4"></i>
                            <p class="text-lg">Select a topic to play video</p>
                        </div>
                    @endif
                </div>

                <!-- Video Info -->
                @if ($selectedTopic)
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
                {{-- List Container: Grid ko hata kar simple vertical stack use kiya hai (space-y-3) --}}
                <div class="space-y-3 p-6">
                    @foreach (json_decode($selectedTopic->attachments, true) as $attachment)
                        {{-- Item Container: File details aur Download button ko flex mein rakha hai. 'group' class added for internal hover effects. --}}
                        <div
                            class="flex items-center justify-between p-3 border border-gray-200 rounded-lg shadow-sm bg-white hover:border-blue-300 transition group">

                            {{-- Left Side: Icon aur File Details (Enhanced Design) --}}
                            <div class="flex items-center flex-1 min-w-0 mr-4">
                                <div class="flex-shrink-0 mr-3">
                                    @php
                                        $extension = strtolower(pathinfo($attachment['name'], PATHINFO_EXTENSION));
                                        $icon = 'fa-file';
                                        if (in_array($extension, ['pdf'])) {
                                            $icon = 'fa-file-pdf text-red-500';
                                        } elseif (in_array($extension, ['doc', 'docx'])) {
                                            $icon = 'fa-file-word text-blue-500';
                                        } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                            $icon = 'fa-file-excel text-green-500';
                                        } elseif (in_array($extension, ['ppt', 'pptx'])) {
                                            $icon = 'fa-file-powerpoint text-orange-500';
                                        } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                            $icon = 'fa-file-image text-purple-500';
                                        } elseif (in_array($extension, ['zip', 'rar'])) {
                                            $icon = 'fa-file-archive text-yellow-500';
                                        }
                                    @endphp
                                    {{-- Icon size increased slightly for emphasis --}}
                                    <i class="fas {{ $icon }} text-2xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    {{-- File Name: Bolder, bigger, and changes color on container hover --}}
                                    <p class="text-base font-bold text-gray-800 truncate group-hover:text-blue-600 transition duration-150"
                                        title="{{ $attachment['name'] }}">
                                        {{ $attachment['name'] }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{-- File size ko KB mein dikhana --}}
                                        {{ round($attachment['size'] / 1024, 2) }} KB
                                    </p>
                                </div>
                            </div>

                            {{-- Right Side: Download Button (Small Size - Same as requested) --}}
                            <a href="{{ $attachment['url'] }}" download="{{ $attachment['name'] }}"
                                class="flex-shrink-0 ml-2 px-2 py-0.5 text-xs font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 transition duration-150 shadow-sm">
                                <i class="fas fa-download mr-1"></i> Download
                            </a>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
        <!-- Sidebar - Chapters & Topics -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden sticky top-6">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4">
                    <h3 class="text-white font-bold text-lg">
                        <i class="fas fa-list mr-2"></i>{{ $course->title }}
                    </h3>
                    <p class="text-blue-100 text-xs mt-1">
                        {{ $course->chapters->count() }} chapters •
                        {{ $course->chapters->sum(fn($c) => $c->topics->count()) }} topics
                    </p>
                </div>

                <!-- Chapters List -->
                <div class="max-h-96 overflow-y-auto">
                    @foreach ($course->chapters as $chapter)
                        <div class="border-b">
                            <!-- Chapter Header -->
                            <button type="button" wire:click="toggleChapter({{ $chapter->id }})"
                                class="w-full flex items-center justify-between p-4 hover:bg-gray-50 transition duration-200">
                                <div class="flex items-start gap-2 flex-1">
                                    <i
                                        class="fas fa-chevron-{{ $expandedChapterId == $chapter->id ? 'down' : 'right' }} text-pink-600 mt-1 text-xs"></i>
                                    <div class="text-left">
                                        <p class="font-semibold text-gray-800 text-sm">{{ $chapter->chapter_title }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $chapter->topics->count() }} topics</p>
                                    </div>
                                </div>
                                @if ($expandedChapterId == $chapter->id)
                                    <i class="fas fa-folder-open text-pink-600 text-sm"></i>
                                @endif
                            </button>

                            <!-- Topics List (Expandable) -->
                            @if ($expandedChapterId == $chapter->id)
                                <div class="bg-gray-50 border-t">
                                    @foreach ($chapter->topics as $topic)
                                        <button type="button" wire:click="selectTopic({{ $topic->id }})"
                                            class="w-full flex items-center gap-3 p-3 ml-6 text-left hover:bg-gray-200 transition duration-200 
                                                {{ $selectedTopicId == $topic->id ? 'bg-pink-100 border-l-4 border-pink-600' : 'border-l-4 border-transparent' }}">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-700 truncate">
                                                    <i class="fas fa-play-circle text-pink-600 mr-2"></i>
                                                    {{ $topic->topic_title }}
                                                </p>
                                            </div>
                                            @if ($selectedTopicId == $topic->id)
                                                <i class="fas fa-check-circle text-pink-600 text-sm"></i>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
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

    <!-- Video.js CSS & JS -->
    <link href="https://vjs.zencdn.net/7.20.3/video-js.css" rel="stylesheet" />
    <script src="https://vjs.zencdn.net/7.20.3/video.min.js"></script>

    <script>
        let player = null;
        // Function to initialize video.js player
        function initializeVideoPlayer() {
            const videoElement = document.getElementById('my-video');

            if (videoElement && !videoElement.classList.contains('vjs-ended')) {
                // Dispose existing player
                if (player) {
                    player.dispose();
                }

                // Initialize new player
                player = videojs('my-video', {
                    controls: true,
                    autoplay: true, // Auto-play when topic is selected
                    preload: 'auto',
                    responsive: true,
                    fluid: true
                });

                player.ready(function() {
                    console.log('Video.js player ready');
                });
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializeVideoPlayer, 500);
        });

        // Listen for Livewire playVideo event
        document.addEventListener('livewire:init', function() {
            Livewire.on('playVideo', function() {
                console.log('Play video event received');

                // Wait for Livewire to update DOM, then initialize and play
                setTimeout(function() {
                    initializeVideoPlayer();
                }, 300);
            });
        });

        // Reinitialize when Livewire updates DOM
        document.addEventListener('livewire:update', function() {
            setTimeout(function() {
                if (document.getElementById('my-video')) {
                    initializeVideoPlayer();
                }
            }, 100);
        });
    </script>
</div>
