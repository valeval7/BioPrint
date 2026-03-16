<?php

namespace App\Listeners;

use App\Models\RegistroAuditoria;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
  public function __construct(protected Request $request) {}

  public function handle(Login $event): void
  {
    $ip = request()->ip();

    $ip = request()->header('X-Forwarded-For')
      ?? request()->header('X-Real-IP')
      ?? request()->ip();

    RegistroAuditoria::create([
      'usuario_id'  => $event->user->id,
      'trabajo_id'  => null,
      'tipo_evento' => 'inicio_sesion',
      'direccion_ip' => $ip,
      'detalle'     => json_encode([
        'user_agent' => request()->userAgent(),
      ]),
      'creado_en'   => now(),
    ]);
  }
}
