<?php

namespace App\Http\Controllers;

use App\Events\TrabajoLiberado;
use App\Models\TrabajoImpresion;
use App\Models\NivelAcceso;
use App\Models\RegistroAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ColaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->nivel_acceso_id === NivelAcceso::PREMIUM) {
            $trabajos        = TrabajoImpresion::with('usuario')
                                               ->orderByDesc('creado_en')
                                               ->get();
            $totalPendientes = TrabajoImpresion::where('estado', 'pendiente')->count();
            $totalHoy        = TrabajoImpresion::where('estado', 'liberado')
                                               ->whereDate('liberado_en', today())
                                               ->count();
        } else {
            $trabajos        = TrabajoImpresion::delUsuario($user->id)
                                               ->orderByDesc('creado_en')
                                               ->get();
            $totalPendientes = 0;
            $totalHoy        = 0;
        }

        return view('dashboard.index', compact('trabajos', 'totalPendientes', 'totalHoy'));
    }

    public function liberar(TrabajoImpresion $trabajo)
    {
        $admin = auth()->user();

        // Solo admin (PREMIUM) puede liberar
        if ($admin->nivel_acceso_id !== NivelAcceso::PREMIUM) {
            return back()->with('error', 'No tienes permiso para liberar trabajos.');
        }

        // Solo trabajos pendientes
        if ($trabajo->estado !== TrabajoImpresion::PENDIENTE) {
            return back()->with('error', 'Este trabajo ya no está pendiente.');
        }

        $dueno = $trabajo->usuario;

        if (!$dueno) {
            return back()->with('error', 'No se encontró el usuario dueño del trabajo.');
        }

        if (empty($dueno->ruta_modelo_facial)) {
            return back()->with('error', "El usuario \"{$dueno->name}\" no tiene modelo facial registrado.");
        }

        $rutaArchivo = Storage::disk('local')->path($trabajo->ruta_archivo_cifrado);
        if (!file_exists($rutaArchivo)) {
            return back()->with('error', 'El archivo del trabajo no existe en el servidor.');
        }

        broadcast(new TrabajoLiberado($trabajo));

        Log::info("[Bioprint] Trabajo #{$trabajo->id} enviado al agente. Estado: pendiente hasta verificación facial.", [
            'usuario'       => $dueno->name,
            'modelo_facial' => $dueno->ruta_modelo_facial,
        ]);

        return back()->with('success', "Trabajo \"{$trabajo->nombre_trabajo}\" enviado al agente. El usuario debe verificar su identidad facial.");
    }


    public function resultado(Request $request, TrabajoImpresion $trabajo)
    {
        if ($request->header('X-Bioprint-Token') !== config('bioprint.agent_token')) {
            Log::warning("[Bioprint] Token inválido en /resultado para trabajo #{$trabajo->id}");
            abort(403, 'Token inválido.');
        }

        $request->validate([
            'exito'   => 'required|boolean',
            'mensaje' => 'nullable|string|max:500',
        ]);

        if ($request->boolean('exito')) {
            $trabajo->update([
                'estado'      => TrabajoImpresion::LIBERADO,
                'liberado_en' => now(),
            ]);

            RegistroAuditoria::registrar(
                RegistroAuditoria::TRABAJO_LIBERADO,
                $trabajo->usuario_id,
                $trabajo->id,
                [
                    'modo'    => $trabajo->modo_impresion,
                    'paginas' => $trabajo->paginas,
                    'mensaje' => $request->mensaje,
                ]
            );

            Log::info("[Bioprint] ✅ Trabajo #{$trabajo->id} marcado como liberado — cara verificada.");

            return response()->json(['ok' => true, 'estado' => 'liberado']);

        } else {
            Log::warning("[Bioprint] ❌ Trabajo #{$trabajo->id} — verificación fallida: {$request->mensaje}. Sigue pendiente.");

            return response()->json(['ok' => true, 'estado' => 'pendiente', 'motivo' => $request->mensaje]);
        }
    }
}