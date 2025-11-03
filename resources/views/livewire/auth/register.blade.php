<div class="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Create an Account</h2>

    @if (session()->has('error'))
        <div class="mb-4 text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="register" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input type="text" wire:model.defer="name" placeholder="Enter your name"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200" />
            @error('name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" wire:model.defer="email" placeholder="Enter your email"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200" />
            @error('email') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" wire:model.defer="password" placeholder="Enter your password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200" />
            @error('password') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input type="password" wire:model.defer="password_confirmation" placeholder="Confirm your password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200" />
        </div>

        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('login') }}" class="text-sm text-primary-600 hover:underline">Already have an account?</a>
            <button type="submit"
                class="px-5 py-2.5 bg-gray-800 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition duration-200">
                Register
            </button>
        </div>
    </form>
</div>
