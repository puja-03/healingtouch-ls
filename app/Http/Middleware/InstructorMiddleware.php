<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'instructor') {
            return redirect()->route('login')->with('error', 'Access denied. Only instructors can access this area.');
        }

        return $next($request);
    }
}