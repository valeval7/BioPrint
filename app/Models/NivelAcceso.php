<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NivelAcceso extends Model
{
    protected $table      = 'nivel_accesos';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'nombre',
        'modo_impresion',
        'descripcion',
    ];

    const BASICO   = 1;
    const ESTANDAR = 2;
    const PREMIUM  = 3;

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'nivel_acceso_id');
    }
}