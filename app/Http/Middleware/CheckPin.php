<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class CheckPin
{
public function handle(Request $request, Closure $next)
{
    if (!$request->session()->has('pin_confirmed')) {
        return redirect()->route('pin.show', ['url' => $request->fullUrl()]);
    }

    return $next($request);
}
}
