<aside class="w-64 bg-white border-r hidden md:block">
    <div class="p-4 border-b">
        <h3 class="text-lg font-semibold">Admin Panel</h3>
    </div>
    <nav class="p-4 space-y-1">
        <a href="{{ route('admin.dashboard') }}" wire:navigate
            class="block px-3 py-2 rounded hover:bg-gray-100">Dashboard</a>
        <a href="{{ route('admin.courses') }}" wire:navigate class="block px-3 py-2 rounded hover:bg-gray-100">Courses</a>
        <a href="{{ route('admin.chapters') }}" wire:navigate
            class="block px-3 py-2 rounded hover:bg-gray-100">Chapters</a>

        <a href="{{ route('admin.topics') }}" wire:navigate class="block px-3 py-2 rounded hover:bg-gray-100">Topics</a>

        <a href="{{ route('admin.instructors') }}" wire:navigate
            class="block px-3 py-2 rounded hover:bg-gray-100">Instructors</a>

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
