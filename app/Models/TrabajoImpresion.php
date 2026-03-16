<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrabajoImpresion extends Model
{
  protected $table      = 'trabajo_impresions';
  public    $timestamps = false;

  protected $fillable = [
    'usuario_id',
    'nombre_trabajo',
    'cups_trabajo_id',
    'ruta_archivo_cifrado',
    'modo_impresion',
    'estado',
    'paginas',
    'liberado_en',
    'expira_en',
  ];

  protected $casts = [
    'creado_en'   => 'datetime',
    'liberado_en' => 'datetime',
    'expira_en'   => 'datetime',
  ];

  const PENDIENTE  = 'pendiente';
  const LIBERADO   = 'liberado';
  const CANCELADO  = 'cancelado';
  const ERROR      = 'error';

  const MODO_BN    = 'bn';
  const MODO_COLOR = 'color';

  public function usuario(): BelongsTo
  {
    return $this->belongsTo(User::class, 'usuario_id');
  }

  public function auditorias(): HasMany
  {
    return $this->hasMany(RegistroAuditoria::class, 'trabajo_id');
  }

  public function scopePendientes($query)
  {
    return $query->where('estado', self::PENDIENTE);
  }

  public function scopeDelUsuario($query, int $usuarioId)
  {
    return $query->where('usuario_id', $usuarioId);
  }
}
