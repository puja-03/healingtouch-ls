<div class="p-6 bg-white rounded-lg shadow-md">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">Chapters</h2>
        {{-- <a href="{{ route('admin.chapters.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Add New chapter
        </a> --}}
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

    <!-- Chapters List -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Topics</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($chapters as $chapter)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="bg-pink-100 text-pink-700 w-8 h-8 rounded-full flex items-center justify-center">
                                {{ $chapter->order_index }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $chapter->chapter_title }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $chapter->course->title }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $chapter->topics->count() }} topics</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium space-x-2">
                            <a href="{{ route('admin.chapters.edit', ['course_id' => $chapter->course_id, 'chapter_id' => $chapter->id]) }}" 
                                class="bg-pink-100 text-pink-700 px-3 py-1 rounded hover:bg-pink-200">
                                Edit
                            </a>
                            <button wire:click="confirmDelete({{ $chapter->id }})"
                                class="bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No chapters found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg max-w-sm mx-auto">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Chapter</h3>
            <p class="text-gray-500 mb-4">Are you sure you want to delete this chapter? All associated topics will also be deleted.</p>
            <div class="flex justify-end space-x-3">
                <button wire:click="$set('showDeleteModal', false)"
                    class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">
                    Cancel
                </button>
                <button wire:click="deleteChapter"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    Delete
                </button>
            </div>
        </div>
    </div>
    @endif
</div>