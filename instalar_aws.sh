#!/bin/bash
# Ejecutar en tu servidor AWS dentro del proyecto Laravel

echo "=== Instalando Laravel Reverb ==="
php artisan install:broadcasting
# Selecciona 'reverb' cuando pregunte

echo "=== Copiando archivos ==="
# ColaController.php → app/Http/Controllers/ColaController.php
# TrabajoLiberado.php → app/Events/TrabajoLiberado.php

echo "=== Agregar ruta del agente en web.php ==="
echo "Agrega esto dentro del grupo middleware('auth'):"
echo "Route::patch('/trabajos/{trabajo}/resultado', [ColaController::class, 'resultado'])->name('trabajos.resultado');"

echo "=== Variables .env a agregar ==="
cat << 'ENV'

# Reverb
REVERB_APP_ID=bioprint
REVERB_APP_KEY=GENERA_UNA_CLAVE_ALEATORIA
REVERB_APP_SECRET=GENERA_UN_SECRET_ALEATORIO
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=ws

BROADCAST_CONNECTION=reverb

# Token del agente
BIOPRINT_AGENT_TOKEN=MISMO_TOKEN_QUE_EN_AGENTE_PY
ENV

echo "=== Crear config/bioprint.php ==="
cat << 'CONFIG'
<?php
return [
    'agent_token' => env('BIOPRINT_AGENT_TOKEN', ''),
];
CONFIG

echo "=== Iniciar Reverb ==="
php artisan reverb:start --host=0.0.0.0 --port=8080 --daemon
