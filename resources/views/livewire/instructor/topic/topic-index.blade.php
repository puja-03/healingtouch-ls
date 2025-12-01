<div class="p-6 bg-white rounded-2xl shadow-md">
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('instructor.chapter', $chapter->course->slug) }}"
                class="text-pink-600 hover:underline text-sm mb-2 block">← Back to Chapters</a>
            <h2 class="text-2xl font-bold">Topics for: {{ $chapter->chapter_title }}</h2>
            <p class="text-gray-600 mt-1">Chapter: {{ $chapter->chapter_title }} | Course: {{ $chapter->course->title }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search topics..."
                class="rounded-lg border-gray-300 px-3 py-2 border" />
            <button wire:click="create"
                class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition">+ New Topic</button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200">{{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-200">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto bg-white rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Topic
                        Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Video
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Attachments</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($topics as $topic)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $topic->topic_title }}</div>
                            @if ($topic->content)
                                <div class="text-sm text-gray-500 mt-1">{{ Str::limit($topic->content, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $topic->order_index }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($topic->video_url)
                                <a href="{{ $topic->video_url }}" target="_blank"
                                    class="text-green-600 hover:text-green-900 bg-green-50 px-2 py-1 rounded text-xs">
                                    View Video
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">No video</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($topic->attachments)
                                @php
                                    $attachments = json_decode($topic->attachments, true);
                                @endphp
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($attachments as $attachment)
                                        <a href="{{ $attachment['url'] }}" target="_blank"
                                            class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs"
                                            title="{{ $attachment['name'] }}">
                                            <i class="fas fa-file mr-1 text-xs"></i>
                                            {{ Str::limit(pathinfo($attachment['name'], PATHINFO_FILENAME), 10) }}
                                            {{ '.' . pathinfo($attachment['name'], PATHINFO_EXTENSION) }}
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">No attachments</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="edit({{ $topic->id }})"
                                class="text-indigo-600 hover:text-indigo-900 mr-4 bg-indigo-50 px-3 py-1 rounded">Edit</button>
                            <button wire:click="confirmDelete({{ $topic->id }})"
                                class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center">
                            <div class="text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                                    </path>
                                </svg>
                                <p class="mt-2">No topics found.</p>
                                <p class="text-sm">Get started by creating your first topic.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $topics->links() }}
    </div>

    @if ($showForm)
        <div wire:key="topic-form-modal">
            <livewire:instructor.topic.topic-form :chapter-id="$chapter->id" :editing-id="$editingId" />
        </div>
    @endif

    @if ($confirmingDelete)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">Confirm Delete</h3>
                <p class="text-sm text-gray-700 mb-6">Are you sure you want to delete this topic? This will also delete
                    its video.</p>
                <div class="flex justify-end gap-3">
                    <button wire:click="cancelDelete"
                        class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">Cancel</button>
                    <button wire:click="delete({{ $deletingId }})"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
