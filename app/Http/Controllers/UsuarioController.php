<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\NivelAcceso;
use App\Models\RegistroAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with('nivelAcceso')->orderBy('nivel_acceso_id')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $niveles = NivelAcceso::all();
        return view('usuarios.create', compact('niveles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|min:8|confirmed',
            'nivel_acceso_id'  => 'required|exists:nivel_accesos,id',
        ]);

        $usuario = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'nivel_acceso_id' => $request->nivel_acceso_id,
            'activo'          => true,
        ]);

        RegistroAuditoria::registrar(
            RegistroAuditoria::CAMBIO_ACL,
            auth()->id(),
            null,
            ['accion' => 'usuario_creado', 'nuevo_usuario_id' => $usuario->id]
        );

        return redirect()->route('usuarios.index')
                         ->with('success', "Usuario {$usuario->name} creado correctamente.");
    }

    public function edit(User $usuario)
    {
        $niveles = NivelAcceso::all();
        return view('usuarios.edit', compact('usuario', 'niveles'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,' . $usuario->id,
            'nivel_acceso_id' => 'required|exists:nivel_accesos,id',
            'password'        => 'nullable|min:8|confirmed',
        ]);

        $nivelAnterior = $usuario->nivel_acceso_id;

        $usuario->fill([
            'name'            => $request->name,
            'email'           => $request->email,
            'nivel_acceso_id' => $request->nivel_acceso_id,
        ]);

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        if ($nivelAnterior !== (int) $request->nivel_acceso_id) {
            RegistroAuditoria::registrar(
                RegistroAuditoria::CAMBIO_ACL,
                auth()->id(),
                null,
                [
                    'accion'         => 'cambio_nivel',
                    'usuario_id'     => $usuario->id,
                    'nivel_anterior' => $nivelAnterior,
                    'nivel_nuevo'    => $request->nivel_acceso_id,
                ]
            );
        }

        return redirect()->route('usuarios.index')
                         ->with('success', "Usuario {$usuario->name} actualizado correctamente.");
    }

    public function toggle(User $usuario)
    {
        $usuario->update(['activo' => !$usuario->activo]);

        RegistroAuditoria::registrar(
            RegistroAuditoria::CAMBIO_ACL,
            auth()->id(),
            null,
            [
                'accion'     => $usuario->activo ? 'usuario_activado' : 'usuario_desactivado',
                'usuario_id' => $usuario->id,
            ]
        );

        $estado = $usuario->activo ? 'activado' : 'desactivado';
        return back()->with('success', "Usuario {$usuario->name} {$estado} correctamente.");
    }
}