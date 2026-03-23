<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class CheckPin
{
public function handle(Request $request, Closure $next)
{
    $confirmed = $request->session()->pull('pin_confirmed');

    if (!$confirmed) {
        return redirect()->route('pin.show', ['url' => $request->fullUrl()]);
    }
    
    return $next($request);
}
}
