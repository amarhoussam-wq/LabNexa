<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // If trying to access admin area, point to admin tab
            if ($request->is('dashboard') || $request->is('dashboard/*')) {
                return route('access', ['tab' => 'admin']);
            }
            return route('access');
        }
    }
}
