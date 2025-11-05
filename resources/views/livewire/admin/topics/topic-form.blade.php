<div class="p-6 bg-white rounded-lg shadow-md">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">
            {{ $topicId ? 'Edit Topic' : 'Create New Topic' }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ $topicId ? 'Update topic information' : 'Add a new topic to your chapter' }}
        </p>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="bg-pink-50 p-6 rounded-lg">
            <div class="grid gap-6">
                <div>
                    <label for="topic_title" class="block text-sm font-medium text-gray-700">Topic Title</label>
                    <input type="text" wire:model="topic_title" id="topic_title"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200">
                    @error('topic_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea wire:model="content" id="content" rows="4"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200"></textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="bg-pink-50 p-4 rounded-lg">
                    <label for="chapter_filter" class="block text-sm font-medium text-gray-700 mb-2">Select Chapter</label>
                    <select wire:model="selectedChapter" id="chapter_filter" 
                        class="w-full rounded-lg border-gray-300 focus:border-pink-500 focus:ring focus:ring-pink-200">
                        <option value="">Select a chapter...</option>
                        @foreach($chapters as $chapter)
                            <option value="{{ $chapter->id }}">{{ $chapter->chapter_title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="order_index" class="block text-sm font-medium text-gray-700">Order</label>
                    <input type="number" wire:model="order_index" id="order_index"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200">
                    @error('order_index')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="video" class="block text-sm font-medium text-gray-700">Video</label>
                    <input type="file" wire:model="video" id="video" accept="video/mp4,video/quicktime"
                        class="mt-1 block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-lg file:border-0
                        file:text-sm file:font-medium
                        file:bg-pink-50 file:text-pink-700
                        hover:file:bg-pink-100">
                    <div wire:loading wire:target="video" class="mt-2 text-sm text-gray-500">
                        Uploading video...
                    </div>
                    @error('video')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($currentVideo)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Current video:</p>
                            <a href="{{ $currentVideo }}" target="_blank" 
                                class="text-pink-600 hover:text-pink-700 text-sm underline">
                                View current video
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.topics', ['chapters_id' => $chaptersId]) }}"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                Cancel
            </a>
            <button type="submit"
                class="px-4 py-2 border border-transparent rounded-lg text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                {{ $topicId ? 'Update Topic' : 'Create Topic' }}
            </button>
        </div>
    </form>
</div>