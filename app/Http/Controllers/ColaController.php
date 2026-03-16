<?php
namespace App\Http\Controllers;

use App\Models\TrabajoImpresion;
use App\Models\NivelAcceso;
use App\Models\RegistroAuditoria;
use Illuminate\Http\Request;

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
        $user = auth()->user();

        if ($trabajo->usuario_id !== $user->id && $user->nivel_acceso_id !== NivelAcceso::PREMIUM) {
            return back()->with('error', 'No tienes permiso para liberar este trabajo.');
        }

        $trabajo->update([
            'estado'      => TrabajoImpresion::LIBERADO,
            'liberado_en' => now(),
        ]);

        RegistroAuditoria::registrar(
            RegistroAuditoria::TRABAJO_LIBERADO,
            $user->id,
            $trabajo->id,
            ['modo' => $trabajo->modo_impresion, 'paginas' => $trabajo->paginas]
        );

        return back()->with('success', "Trabajo \"{$trabajo->nombre_trabajo}\" liberado correctamente.");
    }
}