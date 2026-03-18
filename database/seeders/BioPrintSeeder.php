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

    }
}