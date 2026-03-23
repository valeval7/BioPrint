<?php

namespace App\Events;

use App\Models\TrabajoImpresion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrabajoLiberado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly TrabajoImpresion $trabajo
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('bioprint');
    }

    /**
     * Nombre del evento que recibe el agente Python.
     */
    public function broadcastAs(): string
    {
        return 'trabajo.liberado';
    }

    public function broadcastWith(): array
    {
        return [
            'trabajo_id'     => $this->trabajo->id,
            'nombre_trabajo' => $this->trabajo->nombre_trabajo,
'ruta_archivo'   => \Illuminate\Support\Facades\Storage::disk('local')->path($this->trabajo->ruta_archivo_cifrado),
            'modelo_facial'  => $this->trabajo->usuario->ruta_modelo_facial ?? null,
            'usuario_nombre' => $this->trabajo->usuario->name ?? 'Desconocido',
        ];
    }
}