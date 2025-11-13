<div class="p-6 bg-white rounded-2xl shadow-md">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">My Courses</h2>
        <div class="flex items-center gap-2">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search courses..."
                   class="rounded-lg border-gray-300 px-3 py-2" />
            <button wire:click="create" class="px-4 py-2 bg-pink-600 text-white rounded-lg">+ New Course</button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="w-full  divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('title')">
                            Title 
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('price')">
                            Price
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($courses as $course)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($course->featured_image)
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($course->featured_image) }}" alt="">
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($course->description, 50) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">₹{{ number_format($course->price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button  class="cursor-pointer">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $course->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                             {{ $course->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a wire:click="edit({{ $course->id }})" class="button text-indigo-600 hover:text-indigo-900 bg-green-500 px-2 py-1 rounded-md">
                                        Edit
                                    </a>
                                    <button wire:click="confirmDelete({{ $course->id }})" class="button text-red-600 hover:text-red-900 bg-red-100 px-2 py-1 rounded-md">
                                        Delete
                                    </button>
                                    <a href="{{ route('instructor.chapter', ['courseId' => $course->id]) }}" wire:navigate class="button text-white bg-gray-500 px-2 py-1 rounded-md">
                                                Chapters
                                            </a>
                                                                                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">
                {{ $courses->links() }}
            </div>
        </div>



        @if($showForm)
            <livewire:instructor.course.course-form 
                :editing-id="$editingId" 
                wire:key="course-form-{{ $editingId }}"
                @courseSaved="onCourseSaved"
                @courseCancelled="onCourseCancelled" />
        @endif

        @if($confirmingDelete)
            <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-md w-full">
                    <h3 class="text-lg font-semibold mb-4">Confirm Delete</h3>
                    <p class="text-sm text-gray-700 mb-6">Are you sure you want to delete this course? This will also delete all chapters and topics.</p>
                    <div class="flex justify-end gap-3">
                        <button wire:click="cancelDelete" class="px-4 py-2 border rounded">Cancel</button>
                        <button wire:click="delete({{ $deletingId }})" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
                    </div>
                </div>
            </div>
        @endif
</div>
