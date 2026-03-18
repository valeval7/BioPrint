<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
  use WithoutModelEvents;
  public function run(): void
  {
    DB::table('users')->insert([
      [
        'name' => 'Admin',
        'email' => 'admin@bioprint.com',
        'password' => Hash::make('Aa123456'),
        'nivel_acceso_id' => 3,
        'modo_impresion' => 'color',
        'ruta_modelo_facial' => '/lib/security/howdy/models/vmg.dat',
        'activo' => 1,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'Usuario1',
        'email' => 'usuario1@bioprint.com',
        'password' => Hash::make('123456'),
        'nivel_acceso_id' => 1,
        'modo_impresion' => 'bn',
        'ruta_modelo_facial' => '/lib/security/howdy/models/yara.dat',
        'activo' => 1,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'Usuario2',
        'email' => 'usuario2@bioprint.com',
        'password' => Hash::make('123456'),
        'nivel_acceso_id' => 2,
        'modo_impresion' => 'color',
        'ruta_modelo_facial' => 'modelos/user2.dat',
        'activo' => 0,
        'created_at' => now(),
        'updated_at' => now()
      ]
    ]);
  }
}
