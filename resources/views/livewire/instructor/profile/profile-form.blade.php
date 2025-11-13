<div class="w-full bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
    <h2 class="text-3xl font-bold text-gray-800 mb-2 pb-4 border-b border-gray-100">
        {{ $existingImage ? 'Edit Profile' : 'Create Profile' }}
    </h2>

    @if (session('success'))
        <div class="p-4 mb-6 bg-green-50 text-green-700 rounded-xl border border-green-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                <label class="block font-semibold text-gray-700 mb-2">Specialization</label>
                <input type="text" wire:model="specialization" 
                    class="w-full border-gray-300 rounded-lg mt-1 px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200" 
                    placeholder="e.g. Frontend Developer, UX Designer" />
            </div>
            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                <label class="block font-semibold text-gray-700 mb-3">Profile Image</label>
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <input type="file" wire:model="profile_image" 
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100 transition-all duration-200" />
                        @error('profile_image') 
                            <p class="text-red-600 text-sm mt-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>
                    @if ($existingImage)
                        <div class="ml-6">
                            <img src="{{ $existingImage }}" alt="Profile Image" 
                                class="w-20 h-20 rounded-xl object-cover border-2 border-white shadow-md">
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Website & Experience -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                <label class="block font-semibold text-gray-700 mb-2">Website</label>
                <input type="url" wire:model="website" 
                       class="w-full border-gray-300 rounded-lg mt-1 px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200" 
                       placeholder="https://yourwebsite.com" />
            </div>
            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                <label class="block font-semibold text-gray-700 mb-2">Experience (Years)</label>
                <input type="number" wire:model="experience_years" 
                       class="w-full border-gray-300 rounded-lg mt-1 px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200" 
                       placeholder="5" />
            </div>
            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                <label class="block font-semibold text-gray-700 mb-2">Skills (comma separated)</label>
                <input type="text" wire:model.lazy="skills" 
                    class="w-full border-gray-300 rounded-lg mt-1 px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200" 
                    placeholder="e.g. Laravel, Livewire, Tailwind, Vue.js" />
                <p class="text-sm text-gray-500 mt-2">Separate each skill with a comma</p>
            </div>
        </div>

        <!-- Social Links -->
        <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
            <label class="block font-semibold text-gray-700 mb-4">Social Profiles</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                        </svg>
                        <label class="font-medium text-gray-600">Twitter</label>
                    </div>
                    <input type="url" wire:model="twitter" 
                           class="w-full border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200" 
                           placeholder="https://twitter.com/username" />
                </div>
                <div>
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                        <label class="font-medium text-gray-600">LinkedIn</label>
                    </div>
                    <input type="url" wire:model="linkedin" 
                           class="w-full border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all duration-200" 
                           placeholder="https://linkedin.com/in/username" />
                </div>
                <div>
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                        </svg>
                        <label class="font-medium text-gray-600">YouTube</label>
                    </div>
                    <input type="url" wire:model="youtube" 
                           class="w-full border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:border-transparent transition-all duration-200" 
                           placeholder="https://youtube.com/c/username" />
                </div>
            </div>
        </div>

        <!-- Education & Certifications -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                <label class="block font-semibold text-gray-700 mb-2">Education</label>
                <textarea wire:model="education" rows="3" 
                          class="w-full border-gray-300 rounded-lg mt-1 px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200"
                          placeholder="Your educational background..."></textarea>
            </div>
            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                <label class="block font-semibold text-gray-700 mb-2">Certifications</label>
                <textarea wire:model="certifications" rows="3" 
                          class="w-full border-gray-300 rounded-lg mt-1 px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200"
                          placeholder="Your certifications..."></textarea>
            </div>
        </div>
        <!-- Bio -->
        <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
            <label class="block font-semibold text-gray-700 mb-2">Bio</label>
            <textarea wire:model="bio" rows="4" 
                      class="w-full border-gray-300 rounded-lg mt-1 px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200"
                      placeholder="Tell us about yourself..."></textarea>
        </div>
        <!-- Submit Button -->
        <div class="pt-4 border-t border-gray-200">
            <button type="submit" 
                    class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-8 py-3 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                {{ $existingImage ? 'Update Profile' : 'Create Profile' }}
                <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>
        </div>
    </form>
</div>