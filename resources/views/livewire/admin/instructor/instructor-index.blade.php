<div class="p-6 bg-white rounded-2xl shadow-md">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Instructors</h2>
        <div class="flex items-center gap-2">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search instructors..."
                   class="rounded-lg border-gray-300 px-3 py-2" />
            <button wire:click="create" class="px-4 py-2 bg-pink-600 text-white rounded-lg">+ New</button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($instructors as $index => $instructor)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $loop->iteration + ($instructors->currentPage()-1) * $instructors->perPage() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $instructor->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $instructor->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $instructor->role }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="edit({{ $instructor->id }})" class="text-pink-600 hover:underline mr-3">Edit</button>
                            <button wire:click="confirmDelete({{ $instructor->id }})" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No instructors found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{-- If using pagination from Livewire component --}}
        @if(method_exists($instructors, 'links'))
            {{ $instructors->links() }}
        @endif
    </div>

    {{-- Include the form component / partial --}}
    @if($showForm ?? false)
        @include('livewire.admin.instructor.instructor-form')
    @endif

    {{-- Simple delete confirmation --}}
    @if($confirmingDelete ?? false)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold mb-4">Confirm Delete</h3>
                <p class="text-sm text-gray-700 mb-6">Are you sure you want to delete this instructor? This action cannot be undone.</p>
                <div class="flex justify-end gap-3">
                    <button wire:click="cancelDelete" class="px-4 py-2 border rounded">Cancel</button>
                    <button wire:click="delete({{ $deletingId ?? 0 }})" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>