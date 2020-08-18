<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;

class TokenMiddleware
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
        $secretkey = "impoexpo2020"; 
        $decoded = '';
        //dd($request->hasHeader('authorization'));
        if ($request->hasHeader('authorization') === true) {
            //$file = Storage::get('key.txt');
            $token = $request->token;

            $decoded = JWT::decode($token, $secretkey, array('HS256'));
        //email $decoded->email;
        return $next($decoded);
        }
        
    }
}
