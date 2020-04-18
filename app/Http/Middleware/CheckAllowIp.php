<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use \Symfony\Component\HttpKernel\Exception\HttpException;

class CheckAllowIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $ipList = json_decode(getenv('ALLOW_IP'), '[127.0.0.1]');
        $request = Request();
        if (!in_array($request->ip(), $ipList)) {
            throw new HttpException(401, 'wrong ip ' . $request->ip());
        }

        return $next($request);
    }
}
