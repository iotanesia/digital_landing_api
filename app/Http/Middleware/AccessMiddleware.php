<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\ApiHelper as Helper;
use Firebase\JWT\ExpiredException;

class AccessMiddleware
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
            try {
                $token = $request->bearerToken();
                if(!$token) throw new \Exception("Invalid Access Token", 400);
                if($token){
                    $credentials = Helper::decodeJwt($token);
                }
            } catch(ExpiredException $e) {
                throw new \Exception("Expired Access Token.", 401);
            } catch(\Throwable $e) {
                throw new \Exception("Invalid Access Token.", 401);
            } catch (\Throwable $th) {
                throw $th;
            }
            $request->current_user = $credentials->sub;
            return $next($request);
       } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
       }
    }
}
