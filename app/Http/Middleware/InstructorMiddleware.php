<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstructorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isInstructor()) {
            return redirect()->route('login')->with('error', 'Access denied. Only instructors can access this area.');
        }

        return $next($request);
    }
}