<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroAuditoria extends Model
{
  protected $table      = 'registro_auditorias';
  public    $timestamps = false;

  protected $fillable = [
    'usuario_id',
    'tipo_evento',
    'trabajo_id',
    'direccion_ip',
    'detalle',
  ];

  protected $casts = [
    'creado_en' => 'datetime',
    'detalle'   => 'array',
  ];

  const AUTH_EXITOSA      = 'auth_exitosa';
  const AUTH_FALLIDA      = 'auth_fallida';
  const TRABAJO_LIBERADO  = 'trabajo_liberado';
  const TRABAJO_CANCELADO = 'trabajo_cancelado';
  const INICIO_SESION     = 'inicio_sesion';
  const CIERRE_SESION     = 'cierre_sesion';
  const CAMBIO_ACL        = 'cambio_acl';

  public function usuario(): BelongsTo
  {
    return $this->belongsTo(User::class, 'usuario_id');
  }

  public function trabajo(): BelongsTo
  {
    return $this->belongsTo(TrabajoImpresion::class, 'trabajo_id');
  }

  public static function registrar(
    string  $tipoEvento,
    ?int    $usuarioId = null,
    ?int    $trabajoId = null,
    ?array  $detalle   = null,
    ?string $ip        = null
  ): self {
    return self::create([
      'tipo_evento'  => $tipoEvento,
      'usuario_id'   => $usuarioId,
      'trabajo_id'   => $trabajoId,
      'detalle'      => $detalle ? json_encode($detalle) : null,
      'direccion_ip' => $ip ?? request()->ip(),
    ]);
  }
}
