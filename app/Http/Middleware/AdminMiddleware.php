<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $roleId = isset(Auth::guard('api')->user()->role_id) ? Auth::guard('api')->user()->role_id : null;
        if ($roleId == 1) // is an admin
        {
            return $next($request); // pass the admin
        }
        throw new AccessDeniedHttpException("Admin Restricted", null, '401');

    }
}
