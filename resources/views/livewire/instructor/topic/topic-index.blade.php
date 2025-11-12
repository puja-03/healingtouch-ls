<div class="p-6 bg-white rounded-2xl shadow-md max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('instructor.chapters.index', $chapter->course_id) }}" class="text-pink-600 hover:underline text-sm mb-2 block">← Back to Chapters</a>
            <h2 class="text-2xl font-bold">Topics for {{ $chapter->chapter_title }}</h2>
        </div>
        <div class="flex items-center gap-2">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search topics..."
                   class="rounded-lg border-gray-300 px-3 py-2" />
            <button wire:click="create" class="px-4 py-2 bg-pink-600 text-white rounded-lg">+ New Topic</button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Topic</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Video</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($topics as $topic)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $topic->topic_title }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $topic->order_index }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($topic->video_url)
                                <a href="{{ $topic->video_url }}" target="_blank" class="text-blue-600 hover:underline">View Video</a>
                            @else
                                <span class="text-gray-400 text-xs">No video</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <button wire:click="edit({{ $topic->id }})" class="text-pink-600 hover:underline mr-3">Edit</button>
                            <button wire:click="confirmDelete({{ $topic->id }})" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">No topics found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        @if(method_exists($topics, 'links'))
            {{ $topics->links() }}
        @endif
    </div>

    @if($showForm)
        <livewire:instructor.topic.topic-form 
            :chapter-id="$chapterId"
            :editing-id="$editingId" 
            wire:key="topic-form-{{ $editingId }}"
            @topicSaved="onTopicSaved"
            @topicCancelled="onTopicCancelled" />
    @endif

    @if($confirmingDelete)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold mb-4">Confirm Delete</h3>
                <p class="text-sm text-gray-700 mb-6">Are you sure you want to delete this topic? This will also delete its video.</p>
                <div class="flex justify-end gap-3">
                    <button wire:click="cancelDelete" class="px-4 py-2 border rounded">Cancel</button>
                    <button wire:click="delete({{ $deletingId }})" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
