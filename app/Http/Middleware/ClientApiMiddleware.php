<?php

namespace App\Http\Middleware;

use App\Query\Auth\ClientApi;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class ClientApiMiddleware
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
        $username = trim(Str::lower($_SERVER['PHP_AUTH_USER']));
        $password = $_SERVER['PHP_AUTH_PW'];
        $client = ClientApi::byUsername($username);
        if(!$client) throw new \Exception("User tidak terdaftar.", 400);
        if (!Hash::check($password, $client->password)) throw new \Exception("username atau password salah.",400);
        $request->client = $client;
        return $next($request);
    }
}
