<div class="p-6 bg-white rounded-lg shadow-md">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">
            {{ $chapterId ? 'Edit Chapter' : 'Create New Chapter' }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ $chapterId ? 'Update chapter information' : 'Add a new chapter to your course' }}
        </p>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="bg-pink-50 p-6 rounded-lg">
            <div class="grid gap-6">
                <div>
                    <label for="chapter_title" class="block text-sm font-medium text-gray-700">Chapter Title</label>
                    <input type="text" wire:model="chapter_title" id="chapter_title"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200">
                    @error('chapter_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="order_index" class="block text-sm font-medium text-gray-700">Order</label>
                    <input type="number" wire:model="order_index" id="order_index"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200">
                    @error('order_index')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        <div class="bg-pink-50 p-4 rounded-lg">
            <label for="course_filter" class="block text-sm font-medium text-gray-700 mb-2">Select Course</label>
            <select wire:model="selectedCourse" id="course_filter" 
                class="w-full rounded-lg border-gray-300 focus:border-pink-500 focus:ring focus:ring-pink-200">
                <option value="">Select a course...</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.chapters', ['course_id' => $courseId]) }}"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                Cancel
            </a>
            <button type="submit"
                class="px-4 py-2 border border-transparent rounded-lg text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                {{ $chapterId ? 'Update Chapter' : 'Create Chapter' }}
            </button>
        </div>
    </form>
</div>