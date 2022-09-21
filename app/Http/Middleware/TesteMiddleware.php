<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TesteMiddleware
{
   
    public function handle(Request $request, Closure $next)
    {
           if($result){
             redirect()->route('home');
             return $response = require_once('home');

           }
        return $next($request);
    }
}
