<aside class="w-64 bg-white border-r hidden md:block">
    <div class="p-4 border-b">
        <h3 class="text-lg font-semibold">Admin</h3>
    </div>
    <nav class="p-4 space-y-1">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="block px-3 py-2 rounded hover:bg-gray-100">Dashboard</a>
        <a href="{{ route('admin.courses') }}" wire:navigate class="block px-3 py-2 rounded hover:bg-gray-100">Courses</a>
        <a href="{{ route('admin.courses.create') }}" wire:navigate class="block px-3 py-2 rounded hover:bg-gray-100">Create Course</a>
        <a href="{{ route('admin.chapters') }}" wire:navigate class="block px-3 py-2 rounded hover:bg-gray-100">Chapters</a>
        {{-- <a href="{{ route('admin.chapters.create') }}" wire:navigate class="block px-3 py-2 rounded hover:bg-gray-100">Create Chapter</a> --}}

        <a href="{{ route('admin.topics') }}" wire:navigate class="block px-3 py-2 rounded hover:bg-gray-100">Topics</a>
        {{-- <a href="{{ route('admin.topics.create') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Create Topic</a> --}}

        <form method="POST" action="{{ route('logout') }}" wire:navigate class="mt-4">
            @csrf
            <button type="submit" class="w-full text-left px-3 py-2 rounded hover:bg-gray-100">Logout</button>
        </form>
    </nav>
</aside>
