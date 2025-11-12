 <div class="flex flex-col h-full border-r border-gray-200 bg-gray-50">
     <aside class="w-64 bg-white border-r hidden md:block">
         <div class="p-4 border-b">
             <h3 class="text-lg font-semibold">Admin</h3>
         </div>
         <nav class="p-4 space-y-1">
            <a wire:navigate href="{{ route('instructor.dashboard') }}"
                 class="block px-3 py-2 rounded hover:bg-gray-100">Dashboard</a>
            <a wire:navigate href="{{ route('instructor.courses') }}" 
                 class="block px-3 py-2 rounded hover:bg-gray-100">Courses</a>
             
            <a wire:navigate href="{{ route('instructor.chapter') }}" 
                 class="block px-3 py-2 rounded hover:bg-gray-100 ">Chapters </a>

             <a href="{{ route('instructor.topic') }}" wire:navigate
                 class="block px-3 py-2 rounded hover:bg-gray-100">Topics</a>


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
    
 </div>
