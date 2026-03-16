<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\NivelAcceso;
use App\Models\User;

class BioPrintSeeder extends Seeder
{
    public function run(): void
    {
        // Niveles de acceso
        NivelAcceso::insert([
            [
                'id'             => 1,
                'nombre'         => 'Básico',
                'modo_impresion' => 'bn',
                'descripcion'    => 'Solo impresión en blanco y negro.',
                'creado_en'      => now(),
            ],
            [
                'id'             => 2,
                'nombre'         => 'Estándar',
                'modo_impresion' => 'color',
                'descripcion'    => 'Impresión a color y blanco y negro.',
                'creado_en'      => now(),
            ],
            [
                'id'             => 3,
                'nombre'         => 'Premium',
                'modo_impresion' => 'color',
                'descripcion'    => 'Acceso total al sistema.',
                'creado_en'      => now(),
            ],
        ]);

        // Usuarios de prueba
        User::create([
            'name'            => 'Administrador',
            'email'           => 'admin@bioprint.com',
            'password'        => Hash::make('admin1234'),
            'nivel_acceso_id' => 3,
            'modo_impresion'  => 'color',
            'activo'          => true,
        ]);

        User::create([
            'name'            => 'Usuario Estándar',
            'email'           => 'estandar@bioprint.com',
            'password'        => Hash::make('user1234'),
            'nivel_acceso_id' => 2,
            'modo_impresion'  => 'color',
            'activo'          => true,
        ]);

        User::create([
            'name'            => 'Usuario Básico',
            'email'           => 'basico@bioprint.com',
            'password'        => Hash::make('user1234'),
            'nivel_acceso_id' => 1,
            'modo_impresion'  => 'bn',
            'activo'          => true,
        ]);
    }
}