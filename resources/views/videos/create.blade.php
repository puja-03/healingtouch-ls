@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Upload Video</h2>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('video.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label for="video" class="block text-sm font-medium text-gray-700 mb-2">
                        Choose Video
                    </label>
                    <input type="file" 
                           id="video" 
                           name="video" 
                           accept="video/*" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Video Title
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <button type="submit" 
                        class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Upload Video
                </button>
            </form>
        </div>
    </div>
</div>
@endsection