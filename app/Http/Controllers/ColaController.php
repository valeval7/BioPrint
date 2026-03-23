<?php

namespace App\Http\Controllers;


use App\Models\TrabajoImpresion;
use App\Models\NivelAcceso;
use App\Models\RegistroAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

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

        if ($admin->nivel_acceso_id !== NivelAcceso::PREMIUM) {
            return back()->with('error', 'No tienes permiso para liberar trabajos.');
        }

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

        $rutaArchivo = \Illuminate\Support\Facades\Storage::disk('local')->path($trabajo->ruta_archivo_cifrado);
        if (!file_exists($rutaArchivo)) {
            return back()->with('error', 'El archivo del trabajo no existe en el servidor.');
        }

        $pusher = new Pusher(
            config('broadcasting.connections.reverb.key'),
            config('broadcasting.connections.reverb.secret'),
            config('broadcasting.connections.reverb.app_id'),
            [
                'host'   => config('broadcasting.connections.reverb.options.host'),
                'port'   => config('broadcasting.connections.reverb.options.port'),
                'scheme' => config('broadcasting.connections.reverb.options.scheme'),
                'useTLS' => false,
            ]
        );

        $pusher->trigger('bioprint', 'trabajo.liberado', [
            'trabajo_id'     => $trabajo->id,
            'nombre_trabajo' => $trabajo->nombre_trabajo,
            'modo_impresion' => $trabajo->modo_impresion,
            'ruta_archivo'   => $rutaArchivo,
            'modelo_facial'  => $dueno->ruta_modelo_facial,
            'usuario_nombre' => $dueno->name,
        ]);

        Log::info("[Bioprint] Trabajo #{$trabajo->id} enviado al agente via WebSocket.", [
            'usuario'       => $dueno->name,
            'modelo_facial' => $dueno->ruta_modelo_facial,
        ]);

        return back()->with('success', "Trabajo \"{$trabajo->nombre_trabajo}\" enviado al agente. Esperando verificación facial.");
    }

    public function liberarDesdeAgente(Request $request, $id)
    {
        $trabajo = TrabajoImpresion::findOrFail($id);

        $trabajo->update([
            'estado'      => TrabajoImpresion::LIBERADO,
            'liberado_en' => now(),
        ]);

        RegistroAuditoria::registrar(
            RegistroAuditoria::TRABAJO_LIBERADO,
            $trabajo->usuario_id,
            $trabajo->id,
            ['modo' => $trabajo->modo_impresion, 'paginas' => $trabajo->paginas]
        );

        Log::info("[Bioprint] ✅ Trabajo #{$trabajo->id} liberado — cara verificada.");

        return response()->json(['ok' => true, 'estado' => 'liberado']);
    }

    public function falloFacial(Request $request, $id)
    {
        $trabajo = TrabajoImpresion::findOrFail($id);

        Log::warning("[Bioprint] ❌ Trabajo #{$trabajo->id} — verificación facial fallida. Sigue pendiente.");

        return response()->json(['ok' => true, 'estado' => 'pendiente']);
    }
}