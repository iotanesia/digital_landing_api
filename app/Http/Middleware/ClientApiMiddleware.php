<?php

namespace App\Http\Middleware;

use App\Query\Auth\ClientApi;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        $username = $request->getUser();
        $password = $request->getPassword();
        $client = ClientApi::byUsername($username);
        if(!$client) throw new \Exception("User tidak terdaftar.", 401);
        if (!Hash::check($password, $client->password)) throw new \Exception("username atau password salah.",400);

        Log::info("ip_client-".$request->getClientIp());
        $ip = explode('.',$request->getClientIp());
        $pop = [];
        while($ip){
            $pop[] = implode('.', $ip);
            array_pop($ip);
        }
        $access_validation = [
            'ip_vpn_dki' => in_array('117.102.85',$pop),
            'ip_local' => in_array('127.0.0.1',$pop),
            'ip_registered' => in_array($request->getClientIp(),explode(';',$client->ip_whitelist)),
        ];
        // * check ip access
        // if(!in_array(true,$access_validation)) throw new \Exception("your IP is not allowed to access  - ip ".$request->getClientIp(), 401);
        $request->client = $client;
        return $next($request);
    }
}
