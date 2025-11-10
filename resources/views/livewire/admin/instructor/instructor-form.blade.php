<div class="fixed inset-0 flex items-start justify-center pt-20 z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">{{ $editingId ? 'Edit Instructor' : 'Create Instructor' }}</h3>
                <button wire:click="cancel" class="text-gray-500 hover:text-gray-800">✕</button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" wire:model.defer="form.name" class="w-full mt-1 rounded-lg border-gray-300 px-3 py-2">
                    @error('form.name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" wire:model.defer="form.email" class="w-full mt-1 rounded-lg border-gray-300 px-3 py-2">
                    @error('form.email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Role</label>
                    <select wire:model.defer="form.role" class="w-full mt-1 rounded-lg border-gray-300 px-3 py-2">
                        <option value="">-- Select role --</option>
                        <option value="instructor">Instructor</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                    @error('form.role') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Password <span class="text-xs text-gray-400">(leave empty to keep current)</span></label>
                    <input type="password" wire:model.defer="form.password" class="w-full mt-1 rounded-lg border-gray-300 px-3 py-2">
                    @error('form.password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end items-center gap-3 mt-4">
                    <button type="button" wire:click="cancel" class="px-4 py-2 border rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded">{{ $editingId ? 'Update' : 'Create' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>