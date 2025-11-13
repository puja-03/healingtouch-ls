<div class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center pt-20 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 border border-gray-200">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-800">
                    {{ $editingId ? 'Edit Chapter' : 'Create New Chapter' }}
                </h3>
                <button wire:click="cancel" type="button" 
                        class="text-gray-500 hover:text-gray-800 p-1 rounded-full hover:bg-gray-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-6">
                <!-- Chapter Title -->
                <div>
                    <label for="chapter_title" class="block text-sm font-medium text-gray-700 mb-2">
                        Chapter Title *
                    </label>
                    <input type="text" 
                           id="chapter_title"
                           wire:model="chapter_title" 
                           class="w-full rounded-lg border-gray-300 px-4 py-3 border focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                           placeholder="Enter chapter title (e.g., Introduction to Course)"
                           autofocus>
                    @error('chapter_title') 
                        <span class="text-red-600 text-sm mt-1 block flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </span> 
                    @enderror
                </div>

                <!-- Order Index -->
                <div>
                    <label for="order_index" class="block text-sm font-medium text-gray-700 mb-2">
                        Order Index *
                    </label>
                    <input type="number" 
                           id="order_index"
                           wire:model="order_index" 
                           class="w-full rounded-lg border-gray-300 px-4 py-3 border focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                           placeholder="1"
                           min="1"
                           step="1">
                    @error('order_index') 
                        <span class="text-red-600 text-sm mt-1 block flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </span> 
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">The position of this chapter in the course sequence.</p>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end items-center gap-3 pt-4 border-t border-gray-200">
                    <button type="button" 
                            wire:click="cancel"
                            wire:loading.attr="disabled"
                            class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition disabled:opacity-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="px-6 py-2.5 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition disabled:opacity-50 flex items-center">
                        <span wire:loading.remove wire:target="save">
                            {{ $editingId ? 'Update Chapter' : 'Create Chapter' }}
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>