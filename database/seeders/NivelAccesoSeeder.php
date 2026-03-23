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
        'id'=> 3,
        'nombre' => 'Administrador',
        'modo_impresion' => 'color',
        'descripcion' => 'Acceso total al sistema.',
        'creado_en' => now()
      ],
      [
        'id'=> 2,
        'nombre' => 'Usuario',
        'modo_impresion' => 'bn',
        'descripcion' => 'Usuario normal, impresión en color/blanco y negro.',
        'creado_en' => now()
      ],
      [
        'id'=> 1,
        'nombre' => 'Invitado',
        'modo_impresion' => 'bn',
        'descripcion' => 'Acceso limitado, impresión en blanco y negro.',
        'creado_en' => now()
      ]
    ]);
  }
}
