<div class="sm:px-6">
    <div class="bg-white  rounded-2xl card-shadow overflow-hidden">
        <div class="p-8">
            <!-- Header with icon -->
            <div class="flex items-center mb-8">
                <div
                    class="flex items-center justify-center w-12 h-12 rounded-full bg-primary-100 text-primary-600 mr-4">
                    <i class="fas fa-graduation-cap text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $courseId ? 'Edit Course' : 'Create New Course' }}
                    </h2>
                    <p class="text-gray-500 text-sm mt-1">
                        {{ $courseId ? 'Update your course information' : 'Fill in the details to create a new course' }}
                    </p>
                </div>
            </div>

            <!-- Success Message -->
            @if (session()->has('message'))
                <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                    <div>
                        <p class="text-green-700 font-medium">Success!</p>
                        <p class="text-green-600">{{ session('message') }}</p>
                    </div>
                </div>
            @endif

            <form wire:submit.prevent="save" class="space-y-8">
                <!-- Title & Description Section -->
                <div class="grid grid-cols-1 gap-8">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-heading text-gray-400 mr-2 text-sm"></i>
                            Course Title
                        </label>
                        <div class="relative">
                            <input type="text" wire:model="title" id="title"
                                class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                placeholder="Enter course title">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-pencil-alt text-gray-400"></i>
                            </div>
                        </div>
                        @error('title')
                            <div class="flex items-center mt-2 text-red-500 text-sm">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-align-left text-gray-400 mr-2 text-sm"></i>
                            Description
                        </label>
                        <div class="relative">
                            <textarea wire:model="description" id="description" rows="4"
                                class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                placeholder="Provide a detailed description of your course"></textarea>
                            <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                <i class="fas fa-file-alt text-gray-400 mt-0.5"></i>
                            </div>
                        </div>
                        @error('description')
                            <div class="flex items-center mt-2 text-red-500 text-sm">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Price & Order Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-tag text-gray-400 mr-2 text-sm"></i>
                            Price
                        </label>
                        <div class="relative">

                            <input type="number" wire:model="price" id="price" step="0.01"
                                class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                placeholder="0.00">
                        </div>
                        @error('price')
                            <div class="flex items-center mt-2 text-red-500 text-sm">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                         <script src="https://cdn.tailwindcss.com"></script>       <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                </div>
                <!-- Publish Toggle -->
                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                    <div class="relative inline-block w-12 mr-3 align-middle select-none">
                        <input type="checkbox" wire:model="is_published" id="is_published" class="sr-only peer">
                        <label for="is_published"
                            class="block h-6 w-12 rounded-full bg-blue-400 cursor-pointer transition-colors duration-200 ease-in-out peer-checked:bg-primary-500"></label>
                        <span
                            class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200 ease-in-out peer-checked:translate-x-6"></span>
                    </div>
                    <label for="is_published" class="text-gray-700 font-medium cursor-pointer">
                        <i class="fas fa-globe-americas mr-2 text-primary-500"></i>
                        Publish this course
                    </label>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.courses') }}"
                        class="inline-flex justify-center items-center py-3 px-6 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex justify-center items-center py-3 px-6 border border-gray-200 shadow-sm text-sm font-medium rounded-xl text-black bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        {{ $courseId ? 'Update Course' : 'Create Course' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
