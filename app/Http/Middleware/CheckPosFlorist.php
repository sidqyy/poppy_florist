<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPosFlorist
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('pos_florist')) {
            return redirect()->route('pos.login');
        }

        return $next($request);
    }
}
