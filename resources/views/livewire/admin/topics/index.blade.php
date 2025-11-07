<div class="p-6 bg-white rounded-lg shadow-md">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">Topics</h2>
        <a href="{{ route('admin.topics.create') }}"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            Add New Topics
        </a>
    </div>
    <!-- Search and Filter -->
    <div class="mb-4 flex space-x-4">
        <div class="flex-1">
            <input wire:model="search" type="text" placeholder="Search topics..."
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
        </div>
        <div class="flex-1">
            <select wire:model="chapterId"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                <option value="">All Chapters</option>
                @foreach ($chapters as $chapter)
                    <option value="{{ $chapter->id }}">{{ $chapter->chapter_title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Success/Error Messages -->
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

    <!-- Topics List -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chapter
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Video
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($topics as $topic)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div
                                class="bg-pink-100 text-pink-700 w-8 h-8 rounded-full flex items-center justify-center">
                                {{ $topic->order_index }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $topic->topic_title }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($topic->content, 50) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <ul class="text-sm text-gray-900 list-disc list-inside space-y-1">
                                @foreach ($chapters as $chapter)
                                    {{ $chapter->chapter_title }}
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-6 py-4">
                            @if ($topic->video_url)
                                <a href="{{ $topic->video_url }}" target="_blank"
                                    class="text-pink-600 hover:text-pink-700">
                                    View Video
                                </a>
                            @else
                                <span class="text-gray-400">No video</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium space-x-2">
                            <a href="{{ route('admin.topics.edit', ['chapters_id' => $topic->chapters_id, 'topic_id' => $topic->id]) }}"
                                class="bg-pink-100 text-pink-700 px-3 py-1 rounded hover:bg-pink-200">
                                Edit
                            </a>
                            <button wire:click="confirmDelete({{ $topic->id }})"
                                class="bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        chapters <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No topics found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $topics->links() }}
    </div>

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg max-w-sm mx-auto">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Topic</h3>
                <p class="text-gray-500 mb-4">Are you sure you want to delete this topic? The associated video will also
                    be deleted.</p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('showDeleteModal', false)"
                        class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">
                        Cancel
                    </button>
                    <button wire:click="deleteTopic"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
