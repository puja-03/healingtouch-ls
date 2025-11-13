<div class="p-6 bg-white rounded-2xl shadow-md">
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('instructor.courses') }}" class="text-pink-600 hover:underline text-sm mb-2 block">← Back to Courses</a>
            <h2 class="text-2xl font-bold">Chapters for: {{ $course->title }}</h2>
            <p class="text-gray-600 mt-1">Course: {{ $course->title }}</p>
        </div>
        <div class="flex items-center gap-2">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search chapters..."
                   class="rounded-lg border-gray-300 px-3 py-2 border" />
            <button wire:click="create" class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition">+ New Chapter</button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-200">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto bg-white rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chapter Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($chapters as $chapter)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $chapter->chapter_title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $chapter->order_index }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $chapter->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="edit({{ $chapter->id }})" 
                                    class="text-indigo-600 hover:text-indigo-900 mr-4 bg-indigo-50 px-3 py-1 rounded">Edit</button>
                            <button wire:click="confirmDelete({{ $chapter->id }})" 
                                    class="text-red-600 hover:text-red-900 mr-4 bg-red-50 px-3 py-1 rounded">Delete</button>
                            <a href="{{ route('instructor.topic', ['chapterId' => $chapter->id]) }}" 
                               class="text-green-600 hover:text-green-900 bg-green-50 px-3 py-1 rounded">Topics</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center">
                            <div class="text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <p class="mt-2">No chapters found.</p>
                                <p class="text-sm">Get started by creating your first chapter.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $chapters->links() }}
    </div>

    @if($showForm)
        <div wire:key="chapter-form-modal">
            <livewire:instructor.chapter.chapter-form 
                :course-id="$courseId"
                :editing-id="$editingId" />
        </div>
    @endif

    @if($confirmingDelete)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">Confirm Delete</h3>
                <p class="text-sm text-gray-700 mb-6">Are you sure you want to delete this chapter? This will also delete all topics in this chapter.</p>
                <div class="flex justify-end gap-3">
                    <button wire:click="cancelDelete" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">Cancel</button>
                    <button wire:click="delete({{ $deletingId }})" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>