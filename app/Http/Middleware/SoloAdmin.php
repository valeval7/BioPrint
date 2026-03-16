<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\NivelAcceso;

class SoloAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->nivel_acceso_id !== NivelAcceso::PREMIUM) {
            abort(403, 'Acceso restringido.');
        }
        return $next($request);
    }
}