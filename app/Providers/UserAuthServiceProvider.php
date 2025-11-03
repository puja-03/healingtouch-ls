<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Auth::viaRequest('custom-token', function ($request) {
            return User::where('email', $request->email)->first();
        });
    }
}