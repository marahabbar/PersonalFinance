<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;

class SuperAdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
{
    if (Auth::guard('api')->check() && $request->user()->type >= 2) {
        return $next($request);
    } else {
        $message = ["message" => "Permission Denied"];
        return response($message, 401);
    }
}
}
