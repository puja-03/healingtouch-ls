<div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-start justify-center pt-20 z-50">
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl w-full max-w-2xl mx-4 border border-white/40">
        <div class="p-6">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-800">
                    {{ $editingId ? 'Edit Course' : 'Create New Course' }}
                </h3>
                <button wire:click="cancel" 
                        class="text-gray-500 hover:text-red-500 transition text-xl font-bold">
                    ✕
                </button>
            </div>

            <!-- Success & Error Alerts -->
            @if (session()->has('message'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg border border-green-300 text-sm shadow-sm">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg border border-red-300 text-sm shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Form -->
            <form wire:submit.prevent="save" class="space-y-5">

                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Course Title</label>
                    <input type="text" 
                           wire:model.defer="form.title"
                           class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 shadow-sm">
                    @error('form.title') 
                        <span class="text-red-600 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea wire:model.defer="form.description" rows="4"
                              class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 shadow-sm"></textarea>
                    @error('form.description') 
                        <span class="text-red-600 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                    <input type="number" step="0.01"
                           wire:model.defer="form.price"
                           class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 px-4 py-2.5 shadow-sm">
                    @error('form.price') 
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Publish Checkbox -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_published"
                           wire:model.defer="form.is_published"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="is_published" class="text-sm font-medium text-gray-700">
                        Publish Course
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4 pt-2">
                    <button type="button" 
                            wire:click="cancel"
                            class="px-5 py-2.5 rounded-xl border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 shadow-sm transition">
                        Cancel
                    </button>

                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-blue-600 text-white shadow-md hover:bg-blue-700 transition">
                        {{ $editingId ? 'Update' : 'Create' }} Course
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
