<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class CheckPin
{
public function handle(Request $request, Closure $next)
{
    if (!$request->session()->has('pin_verified')) {
        return redirect()->route('pin.show', ['url' => $request->fullUrl()]);
    }

    $request->session()->forget('pin_verified');

    return $next($request);
}
}
