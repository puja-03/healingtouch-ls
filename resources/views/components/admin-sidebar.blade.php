<aside class="w-64 bg-white border-r hidden md:block">
    <div class="p-4 border-b">
        <h3 class="text-lg font-semibold">Admin</h3>
    </div>
    <nav class="p-4 space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Dashboard</a>
        <a href="{{ route('admin.courses') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Courses</a>
        <a href="{{ route('admin.courses.create') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Create Course</a>
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full text-left px-3 py-2 rounded hover:bg-gray-100">Logout</button>
        </form>
    </nav>
</aside>
