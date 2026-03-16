<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\NivelAcceso;
use App\Models\TrabajoImpresion;
use App\Models\RegistroAuditoria;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable;
  use HasApiTokens;


  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'nivel_acceso_id',
    'modo_impresion',
    'ruta_modelo_facial',
    'activo',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function nivelAcceso(): BelongsTo
  {
    return $this->belongsTo(NivelAcceso::class, 'nivel_acceso_id');
  }

  public function trabajos(): HasMany
  {
    return $this->hasMany(TrabajoImpresion::class, 'usuario_id');
  }

  public function trabajosPendientes(): HasMany
  {
    return $this->trabajos()->where('estado', TrabajoImpresion::PENDIENTE);
  }

  public function auditorias(): HasMany
  {
    return $this->hasMany(RegistroAuditoria::class, 'usuario_id');
  }

  public function puedeImprimirColor(): bool
  {
    return $this->modo_impresion === 'color';
  }

  public function comandoLp(string $impresora, string $archivo): string
  {
    $modo = $this->puedeImprimirColor()
      ? '-o ColorModel=CMYK'
      : '-o ColorModel=Gray';

    return "lp -d {$impresora} {$modo} {$archivo}";
  }

  protected static function booted(): void
  {
    static::saving(function (User $user) {
      if ($user->isDirty('nivel_acceso_id')) {
        $user->modo_impresion = $user->nivel_acceso_id === NivelAcceso::BASICO
          ? 'bn'
          : 'color';
      }
    });
  }
}
