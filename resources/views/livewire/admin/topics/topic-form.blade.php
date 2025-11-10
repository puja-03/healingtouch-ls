<div class="p-6 bg-white rounded-2xl shadow-md max-w-5xl mx-auto">
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900">{{ $topicId ? 'Edit Topic' : 'Create New Topic' }}</h2>
        <p class="mt-1 text-sm text-gray-600">Follow the steps to create or edit a topic.</p>
    </div>
    
    <div class="flex items-center justify-between mb-8 relative">
        @foreach ([1 => 'Course & Chapter', 2 => 'Topic Details', 3 => 'Upload Video'] as $index => $label)
            <div class="flex flex-col items-center flex-1">
                <div class="relative">
                    <div
                        class="w-10 h-10 flex items-center justify-center rounded-full text-white font-semibold
                        {{ $step >= $index ? 'bg-pink-600' : 'bg-gray-300' }}">
                        {{ $index }}
                    </div>
                    @if($index < 3)
                        <div class="absolute top-1/2 right-[-50%] transform -translate-y-1/2 w-full h-1
                            {{ $step > $index ? 'bg-pink-500' : 'bg-gray-200' }}">
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
        </div>


    <form wire:submit.prevent="save" class="space-y-8">

        {{-- STEP 1: Course & Chapter Selection --}}
        @if($step === 1)
        <div class="bg-pink-50 p-6 rounded-xl shadow-inner">
            <h3 class="text-lg font-semibold text-pink-700 mb-4">Step 1: Select Course and Chapter</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Course Selection --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Course <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="selectedCourse" 
                        wire:change="$refresh"
                        class="w-full rounded-lg border-gray-300 focus:ring-pink-300 focus:border-pink-500"
                    >
                        <option value="">-- Select Course --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">
                                {{ $course->title ?? $course->course_title ?? $course->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('selectedCourse') 
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p> 
                    @enderror
                </div>

                {{-- Chapter Selection --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Chapter <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="selectedChapter"
                        class="w-full rounded-lg border-gray-300 focus:ring-pink-300 focus:border-pink-500"
                        @if(!$selectedCourse) disabled @endif
                    >
                        <option value="">-- Select Chapter --</option>
                        @if($selectedCourse)
                            @forelse($chapters as $chapter)
                                <option value="{{ $chapter['id'] }}">
                                    {{ $chapter['chapter_title'] }}
                                </option>
                            @empty
                                <option value="" disabled>No chapters found for this course</option>
                            @endforelse
                        @else
                            <option value="" disabled>Please select a course first</option>
                        @endif
                    </select>
                    @error('selectedChapter') 
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p> 
                    @enderror
                    
                    @if($selectedCourse && empty($chapters))
                        <p class="text-orange-600 text-sm mt-2">
                            ⚠️ No chapters available for the selected course. 
                            Please add chapters to this course first.
                        </p>
                    @endif
                </div>
            </div>
        </div>
        @endif

               {{-- STEP 2: Topic Details --}}
        @if($step === 2)
        <div class="bg-pink-50 p-6 rounded-xl shadow-inner">
            <h3 class="text-lg font-semibold text-pink-700 mb-4">Step 2: Topic Details</h3>
            <div class="grid gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Topic Title</label>
                    <input type="text" wire:model="topic_title" class="w-full rounded-lg border-gray-300 mt-1 focus:ring-pink-300 focus:border-pink-500">
                    @error('topic_title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea wire:model="content" rows="4" class="w-full rounded-lg border-gray-300 mt-1 focus:ring-pink-300 focus:border-pink-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Order</label>
                    <input type="number" wire:model="order_index" class="w-full rounded-lg border-gray-300 mt-1 focus:ring-pink-300 focus:border-pink-500">
                </div>
            </div>
        </div>
        @endif

        {{-- STEP 3: Upload Video --}}
        @if($step === 3)
        <div class="bg-pink-50 p-6 rounded-xl shadow-inner">
            <h3 class="text-lg font-semibold text-pink-700 mb-4">Step 3: Upload Video</h3>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-pink-500 transition">
                <div x-data="{ isUploading: false, progress: 0 }"
                     x-on:livewire-upload-start="isUploading = true"
                     x-on:livewire-upload-finish="isUploading = false; progress = 0"
                     x-on:livewire-upload-error="isUploading = false"
                     x-on:livewire-upload-progress="progress = $event.detail.progress">
                    
                    <input type="file" wire:model="video" accept="video/mp4,video/quicktime" class="hidden" id="videoUpload">
                    <label for="videoUpload" class="cursor-pointer block">
                        <svg class="mx-auto h-10 w-10 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-700">Click to upload video or drag here</p>
                        <p class="text-xs text-gray-500">MP4 or MOV, up to 100MB</p>
                    </label>

                    <!-- Progress Bar -->
                    <div x-show="isUploading" class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-pink-600 h-2.5 rounded-full transition-all duration-150" x-bind:style="'width: ' + progress + '%'"></div>
                        </div>
                        <p class="text-sm text-gray-600 mt-2" x-text="'Upload Progress: ' + progress + '%'"></p>
                    </div>
                </div>
            </div>

            @error('video') 
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p> 
            @enderror

            @if($video)
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-700">Video selected: {{ $video->getClientOriginalName() }}</p>
                </div>
            @endif

            @if($currentVideo)
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-700 mb-1">Current Video:</p>
                    <a href="{{ $currentVideo }}" target="_blank" class="text-pink-600 hover:underline">View current video</a>
                </div>
            @endif
        </div>
        @endif

        {{-- Navigation Buttons --}}
         <div class="flex justify-between items-center mt-8">
            @if($step > 1)
                <button type="button" wire:click="prevStep"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
                    ← Previous
                </button>
            @endif

            @if($step < 3)
                <button type="button" wire:click="nextStep"
                    class="px-6 py-2 rounded-lg bg-pink-600 text-white hover:bg-pink-700 disabled:opacity-50"
                    {{ !$selectedCourse && $step === 1 ? 'disabled' : '' }}>
                    Next →
                </button>
            @else
                <button type="submit"
                    class="px-6 py-2 rounded-lg bg-pink-600 text-white hover:bg-pink-700">
                    {{ $topicId ? 'Update Topic' : 'Create Topic' }}
                </button>
            @endif
        </div>

    </form>
</div>
