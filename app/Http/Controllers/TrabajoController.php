<?php
namespace App\Http\Controllers;

use App\Models\TrabajoImpresion;
use App\Models\RegistroAuditoria;
use Illuminate\Http\Request;

class TrabajoController extends Controller
{
    // Retorna trabajos pendientes con ruta_modelo_facial del usuario
    public function pendientes()
    {
        $trabajos = TrabajoImpresion::where('estado', 'pendiente')
            ->with('usuario')
            ->get()
            ->map(fn($t) => [
                'id'                  => $t->id,
                'nombre_trabajo'      => $t->nombre_trabajo,
                'modo_impresion'      => $t->modo_impresion,
                'paginas'             => $t->paginas,
                'ruta_archivo_cifrado'=> $t->ruta_archivo_cifrado,
                'ruta_modelo_facial'  => $t->usuario->ruta_modelo_facial ?? null,
            ]);

        return response()->json($trabajos);
    }

    // Libera un trabajo
    public function liberar($id)
    {
        $trabajo = TrabajoImpresion::findOrFail($id);
        $trabajo->update([
            'estado'      => 'liberado',
            'liberado_en' => now(),
        ]);

        RegistroAuditoria::create([
            'usuario_id'   => $trabajo->usuario_id,
            'trabajo_id'   => $trabajo->id,
            'tipo_evento'  => 'trabajo_liberado',
            'direccion_ip' => request()->ip(),
            'detalle'      => json_encode(['nombre' => $trabajo->nombre_trabajo]),
            'creado_en'    => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    // Cancela un trabajo
    public function cancelar($id)
    {
        $trabajo = TrabajoImpresion::findOrFail($id);
        $trabajo->update(['estado' => 'cancelado']);

        RegistroAuditoria::create([
            'usuario_id'   => $trabajo->usuario_id,
            'trabajo_id'   => $trabajo->id,
            'tipo_evento'  => 'trabajo_cancelado',
            'direccion_ip' => request()->ip(),
            'detalle'      => json_encode(['nombre' => $trabajo->nombre_trabajo]),
            'creado_en'    => now(),
        ]);

        return response()->json(['ok' => true]);
    }
}