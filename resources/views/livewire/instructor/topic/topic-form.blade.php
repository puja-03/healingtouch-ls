<div class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center pt-20 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 border border-gray-200 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-800">
                    {{ $editingId ? 'Edit Topic' : 'Create New Topic' }}
                </h3>
                <button wire:click="cancel" type="button"
                    class="text-gray-500 hover:text-gray-800 p-1 rounded-full hover:bg-gray-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-6">
                <!-- Topic Title -->
                <div>
                    <label for="topic_title" class="block text-sm font-medium text-gray-700 mb-2">
                        Topic Title *
                    </label>
                    <input type="text" id="topic_title" wire:model="topic_title"
                        class="w-full rounded-lg border-gray-300 px-4 py-3 border focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                        placeholder="Enter topic title" autofocus>
                    @error('topic_title')
                        <span class="text-red-600 text-sm mt-1 block flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                        Content
                    </label>
                    <textarea id="content" wire:model="content" rows="4"
                        class="w-full rounded-lg border-gray-300 px-4 py-3 border focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                        placeholder="Enter topic content (optional)"></textarea>
                    @error('content')
                        <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Order Index -->
                <div>
                    <label for="order_index" class="block text-sm font-medium text-gray-700 mb-2">
                        Order Index *
                    </label>
                    <input type="number" id="order_index" wire:model="order_index"
                        class="w-full rounded-lg border-gray-300 px-4 py-3 border focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                        placeholder="1" min="1" step="1">
                    @error('order_index')
                        <span class="text-red-600 text-sm mt-1 block flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">The position of this topic in the chapter sequence.</p>
                </div>

                <!-- Video Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Video
                    </label>
                    <div
                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-pink-500 transition mt-2">
                        <div x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true"
                            x-on:livewire-upload-finish="isUploading = false; progress = 0"
                            x-on:livewire-upload-error="isUploading = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress">

                            <input type="file" wire:model="video" accept="video/mp4,video/quicktime" class="hidden"
                                id="videoUpload">
                            <label for="videoUpload" class="cursor-pointer block">
                                <svg class="mx-auto h-10 w-10 text-pink-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-700">Click to upload video or drag here</p>
                                <p class="text-xs text-gray-500">MP4 or MOV, up to 100MB</p>
                            </label>

                            <!-- Progress Bar -->
                            <div x-show="isUploading" class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-pink-600 h-2.5 rounded-full transition-all duration-150"
                                        x-bind:style="'width: ' + progress + '%'"></div>
                                </div>
                                <p class="text-sm text-gray-600 mt-2" x-text="'Upload Progress: ' + progress + '%'"></p>
                            </div>
                        </div>
                    </div>

                    @if ($video)
                        <div class="mt-3 p-3 bg-blue-50 rounded">
                            <p class="text-sm font-medium text-blue-700">Video selected:
                                {{ $video->getClientOriginalName() }}</p>
                        </div>
                    @endif

                    @if ($currentVideo)
                        <div class="mt-3 p-3 bg-gray-50 rounded">
                            <p class="text-sm font-medium text-gray-700">Current Video:</p>
                            <a href="{{ $currentVideo }}" target="_blank"
                                class="text-pink-600 hover:underline text-sm">View Current Video</a>
                        </div>
                    @endif

                    @error('video')
                        <span class="text-red-600 text-sm mt-2 block flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Attachments (Optional)
                    </label>
                    <div
                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-pink-500 transition mt-2">
                        <div x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true"
                            x-on:livewire-upload-finish="isUploading = false; progress = 0"
                            x-on:livewire-upload-error="isUploading = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress">

                            <input type="file" wire:model="attachments" multiple
                                accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar,.jpg,.jpeg,.png"
                                class="hidden" id="attachmentsUpload">
                            <label for="attachmentsUpload" class="cursor-pointer block">
                                <svg class="mx-auto h-10 w-10 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-700">Click to upload attachments or drag here</p>
                                <p class="text-xs text-gray-500">PDF, DOC, PPT, XLS, Images, ZIP, etc. (Max 50MB each)
                                </p>
                            </label>

                            <!-- Progress Bar -->
                            <div x-show="isUploading" class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-150"
                                        x-bind:style="'width: ' + progress + '%'"></div>
                                </div>
                                <p class="text-sm text-gray-600 mt-2" x-text="'Upload Progress: ' + progress + '%'">
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Selected New Attachments -->
                    @if (count($attachments) > 0)
                        <div class="mt-3">
                            <p class="text-sm font-medium text-gray-700 mb-2">New Attachments:</p>
                            <div class="space-y-2">
                                @foreach ($attachments as $index => $attachment)
                                    <div class="flex items-center justify-between p-2 bg-blue-50 rounded">
                                        <div class="flex items-center">
                                            <i class="fas fa-file text-blue-600 mr-2"></i>
                                            <span
                                                class="text-sm text-gray-700">{{ $attachment->getClientOriginalName() }}</span>
                                            <span class="text-xs text-gray-500 ml-2">
                                                ({{ round($attachment->getSize() / 1024, 2) }} KB)
                                            </span>
                                        </div>
                                        <button type="button" wire:click="removeNewAttachment({{ $index }})"
                                            class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Existing Attachments -->
                    @if (count($currentAttachments) > 0)
                        <div class="mt-3">
                            <p class="text-sm font-medium text-gray-700 mb-2">Existing Attachments:</p>
                            <div class="space-y-2">
                                @foreach ($currentAttachments as $index => $attachment)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                        <div class="flex items-center">
                                            <i class="fas fa-file text-gray-600 mr-2"></i>
                                            <a href="{{ $attachment['url'] }}" target="_blank"
                                                class="text-sm text-blue-600 hover:underline">
                                                {{ $attachment['name'] }}
                                            </a>
                                            <span class="text-xs text-gray-500 ml-2">
                                                ({{ round($attachment['size'] / 1024, 2) }} KB)
                                            </span>
                                        </div>
                                        <button type="button" wire:click="removeAttachment({{ $index }})"
                                            class="text-red-500 hover:text-red-700"
                                            onclick="return confirm('Are you sure you want to delete this attachment?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @error('attachments.*')
                        <span class="text-red-600 text-sm mt-2 block flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex justify-end items-center gap-3 pt-4 border-t border-gray-200">
                    <button type="button" wire:click="cancel" wire:loading.attr="disabled"
                        class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition disabled:opacity-50">
                        Cancel
                    </button>
                    <button wire:submit.prevent="save" wire:loading.attr="disabled"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 flex items-center">
                        <span wire:loading.remove wire:target="save">
                            {{ $editingId ? 'Update Topic' : 'Create Topic' }}
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
