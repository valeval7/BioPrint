<?php

namespace App\Http\Controllers;

use App\Models\TrabajoImpresion;
use App\Models\RegistroAuditoria;
use Illuminate\Http\Request;

class TrabajoController extends Controller
{
    // Retorna trabajos pendientes
    public function pendientes()
    {
        $trabajos = TrabajoImpresion::where('estado', 'pendiente')
            ->with('usuario')
            ->get();

        return response()->json($trabajos);
    }

    // Libera un trabajo
    public function liberar($id)
    {
        $trabajo = TrabajoImpresion::findOrFail($id);
        $trabajo->update(['estado' => 'liberado']);

        RegistroAuditoria::create([
            'usuario_id'  => $trabajo->usuario_id,
            'trabajo_id'  => $trabajo->id,
            'tipo_evento' => 'trabajo_liberado',
            'direccion_ip'=> request()->ip(),
            'detalle'     => json_encode(['nombre' => $trabajo->nombre_trabajo]),
            'creado_en'   => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    // Cancela un trabajo
    public function cancelar($id)
    {
        $trabajo = TrabajoImpresion::findOrFail($id);
        $trabajo->update(['estado' => 'cancelado']);

        RegistroAuditoria::create([
            'usuario_id'  => $trabajo->usuario_id,
            'trabajo_id'  => $trabajo->id,
            'tipo_evento' => 'trabajo_cancelado',
            'direccion_ip'=> request()->ip(),
            'detalle'     => json_encode(['nombre' => $trabajo->nombre_trabajo]),
            'creado_en'   => now(),
        ]);

        return response()->json(['ok' => true]);
    }
}