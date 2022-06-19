<?php

namespace App\Http\Middleware;

use App\Query\LogPrescreening;
use Closure;
use Illuminate\Http\Request;

class LogPrescreeningMiddleware
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
        LogPrescreening::store($request);
        return $next($request);
    }
}
