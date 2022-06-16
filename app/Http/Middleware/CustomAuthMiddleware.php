<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class CustomAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Add new header 'Authorization' to request
        $request->headers->add(['Authorization' => 'ABCD']);

        if ($request->hasHeader('Authorization')) {
            $request->user = User::createUserFromToken($request->header('Authorization'));
        }

        if (!$request->user) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
