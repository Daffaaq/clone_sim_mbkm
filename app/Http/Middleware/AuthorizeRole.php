<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorizeRole
{
    /*
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role = '')
    {
        // dd($role);
        if (Auth::check()) {
            if (Auth::user()->hasRole($role)) {
                return $next($request);
            }
            abort(403);
        }
        abort(401);
    }
}
