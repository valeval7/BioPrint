<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NivelAccesoSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('nivel_accesos')->insert([
      [
        'nombre' => 'Administrador',
        'modo_impresion' => 'color',
        'descripcion' => 'Acceso total al sistema',
        'creado_en' => now()
      ],
      [
        'nombre' => 'Usuario',
        'modo_impresion' => 'bn',
        'descripcion' => 'Usuario normal del sistema',
        'creado_en' => now()
      ],
      [
        'nombre' => 'Invitado',
        'modo_impresion' => 'bn',
        'descripcion' => 'Acceso limitado',
        'creado_en' => now()
      ]
    ]);
  }
}
