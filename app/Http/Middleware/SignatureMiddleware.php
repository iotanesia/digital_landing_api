<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Signature;
class SignatureMiddleware
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
        try {
            if(!$request->header('x-client-key')) throw new \Exception("Unauthorized", 401);
            if(!$request->header('x-signature')) throw new \Exception("Unauthorized", 401);
            if(!Signature::verified($request)) throw new \Exception("Invalid Signature", 400);
            return $next($request);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
