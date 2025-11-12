<div class="fixed inset-0 flex items-start justify-center pt-20 z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">{{ $editingId ? 'Edit Chapter' : 'Create New Chapter' }}</h3>
                <button wire:click="cancel" class="text-gray-500 hover:text-gray-800">✕</button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Chapter Title</label>
                    <input type="text" wire:model.defer="form.chapter_title" 
                           class="w-full mt-1 rounded-lg border-gray-300 px-3 py-2">
                    @error('form.chapter_title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Order Index</label>
                    <input type="number" wire:model.defer="form.order_index" 
                           class="w-full mt-1 rounded-lg border-gray-300 px-3 py-2">
                    @error('form.order_index') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end items-center gap-3 mt-6">
                    <button type="button" wire:click="cancel" class="px-4 py-2 border rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded">{{ $editingId ? 'Update' : 'Create' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
