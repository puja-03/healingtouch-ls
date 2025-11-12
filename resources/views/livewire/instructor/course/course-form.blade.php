<div class="fixed inset-0 flex items-start justify-center pt-20 z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">{{ $editingId ? 'Edit Course' : 'Create New Course' }}</h3>
                <button wire:click="cancel" class="text-gray-500 hover:text-gray-800">✕</button>
            </div>
             @if (session()->has('message'))
                <div class="mb-4 p-3 bg-green-50 text-green-700 rounded border border-green-200 text-sm">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-3 bg-red-50 text-red-700 rounded border border-red-200 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Course Title</label>
                    <input type="text" wire:model.defer="form.title" 
                           class="w-full mt-1 rounded-lg border-gray-300 px-3 py-2">
                    @error('form.title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea wire:model.defer="form.description" rows="4"
                              class="w-full mt-1 rounded-lg border-gray-300 px-3 py-2"></textarea>
                    @error('form.description') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" step="0.01" wire:model.defer="form.price" 
                           class="w-full mt-1 rounded-lg border-gray-300 px-3 py-2">
                    @error('form.price') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_published" wire:model.defer="form.is_published" 
                           class="rounded border-gray-300">
                    <label for="is_published" class="text-sm font-medium text-gray-700">Publish Course</label>
                </div>   
                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="cancel" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700">
                        {{ $editingId ? 'Update' : 'Create' }} Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    </form>
</div>