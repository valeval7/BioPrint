<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trabajo;

class ColaController extends Controller
{
    /**
     * Mostrar la cola
     */
    public function index()
    {
        // Si es admin ve todo, si no solo lo suyo
        if (auth()->user()->nivel_acceso_id === \App\Models\NivelAcceso::PREMIUM) {
            $trabajos = Trabajo::with('usuario')
                ->orderBy('creado_en', 'asc')
                ->get();
        } else {
            $trabajos = Trabajo::where('usuario_id', auth()->id())
                ->orderBy('creado_en', 'asc')
                ->get();
        }

        $totalPendientes = Trabajo::where('estado', 'pendiente')->count();
        $totalHoy = Trabajo::whereDate('creado_en', now())->count();

        return view('cola.index', compact('trabajos', 'totalPendientes', 'totalHoy'));
    }

    /**
     * Liberar un trabajo (AQUÍ se ejecuta Python)
     */
    public function liberar($id)
    {
        $trabajo = Trabajo::findOrFail($id);

        // Cambiar estado
        $trabajo->estado = 'liberado';
        $trabajo->save();

        // Ejecutar script Python
        try {
            exec("python3 /opt/bioprint/agente.py > /dev/null 2>&1 &");
        } catch (\Exception $e) {
            \Log::error("Error ejecutando Python: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Trabajo liberado correctamente');
    }

    /**
     * Cancelar trabajo (opcional)
     */
    public function cancelar($id)
    {
        $trabajo = Trabajo::findOrFail($id);

        $trabajo->estado = 'cancelado';
        $trabajo->save();

        return redirect()->back()->with('success', 'Trabajo cancelado');
    }
}