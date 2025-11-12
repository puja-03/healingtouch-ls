<div class="fixed inset-0 flex items-start justify-center pt-20 z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">{{ $editingId ? 'Edit Topic' : 'Create New Topic' }}</h3>
                <button wire:click="cancel" class="text-gray-500 hover:text-gray-800">✕</button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Topic Title</label>
                    <input type="text" wire:model.defer="form.topic_title" 
                           class="w-full mt-1 rounded-lg border-gray-300 px-3 py-2">
                    @error('form.topic_title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea wire:model.defer="form.content" rows="4"
                              class="w-full mt-1 rounded-lg border-gray-300 px-3 py-2"></textarea>
                    @error('form.content') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Order Index</label>
                    <input type="number" wire:model.defer="form.order_index" 
                           class="w-full mt-1 rounded-lg border-gray-300 px-3 py-2">
                    @error('form.order_index') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Upload Video</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-pink-500 transition mt-2">
                        <div x-data="{ isUploading: false, progress: 0 }"
                             x-on:livewire-upload-start="isUploading = true"
                             x-on:livewire-upload-finish="isUploading = false; progress = 0"
                             x-on:livewire-upload-error="isUploading = false"
                             x-on:livewire-upload-progress="progress = $event.detail.progress">
                            
                            <input type="file" wire:model="video" accept="video/mp4,video/quicktime" 
                                   class="hidden" id="videoUpload">
                            <label for="videoUpload" class="cursor-pointer block">
                                <svg class="mx-auto h-10 w-10 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
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

                    @if($video)
                        <div class="mt-3 p-3 bg-blue-50 rounded">
                            <p class="text-sm font-medium text-blue-700">Video selected: {{ $video->getClientOriginalName() }}</p>
                        </div>
                    @endif

                    @if($currentVideo)
                        <div class="mt-3 p-3 bg-gray-50 rounded">
                            <p class="text-sm font-medium text-gray-700">Current Video:</p>
                            <a href="{{ $currentVideo }}" target="_blank" class="text-pink-600 hover:underline text-sm">View Current Video</a>
                        </div>
                    @endif

                    @error('video') <span class="text-red-600 text-sm mt-2 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end items-center gap-3 mt-6">
                    <button type="button" wire:click="cancel" class="px-4 py-2 border rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded">{{ $editingId ? 'Update' : 'Create' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
