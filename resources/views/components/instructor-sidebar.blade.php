 <aside class="w-64 bg-white border-r hidden md:block">
    <div class="p-4 border-b">
        <h3 class="text-lg font-semibold">Instructor Panel</h3>
    </div>
    <nav class="p-4 space-y-1">
        <a href="{{ route('instructor.dashboard') }}" wire:navigate class="block px-3 py-2 rounded hover:bg-gray-400">Dashboard</a>
        <a href="{{ route('instructor.courses') }}" wire:navigate class="block px-3 py-2 rounded hover:bg-gray-400">Courses</a>
        {{-- <a href="{{ route('instructor.chapter') }}" wire:navigate class="block px-3 py-2 rounded hover:bg-gray-100">Chapters</a> --}}
         
        {{-- <a href="{{ route('instructor.topics') }}" wire:navigate class="block px-3 py-2 rounded hover:bg-gray-100">Topics</a> --}}

        <div class="ml-3">
                 <p class="text-base font-medium text-gray-700">{{ auth()->user()->name }}</p>
                 <form method="POST" action="{{ route('logout') }}"
                     class="text-sm font-medium text-gray-500 hover:text-gray-700">
                     @csrf
                     <button type="submit" class="text-red-600">Logout</button>
                 </form>
             </div>
    </nav>
</aside>
