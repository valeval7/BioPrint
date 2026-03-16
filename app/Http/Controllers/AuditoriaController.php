<?php
namespace App\Http\Controllers;

use App\Models\RegistroAuditoria;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = RegistroAuditoria::with(['usuario', 'trabajo'])
                                  ->orderByDesc('creado_en');

        if ($request->filled('tipo_evento')) {
            $query->where('tipo_evento', $request->tipo_evento);
        }

        $registros = $query->paginate(20);

        return view('auditoria.index', compact('registros'));
    }
}