@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-white">Gestión de Usuarios</h1>
        <p class="text-slate-400 text-sm mt-1">Administra los niveles de acceso y perfiles biométricos</p>
    </div>
    <a href="{{ route('usuarios.create') }}"
       class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow-lg shadow-blue-900">
        + Nuevo Usuario
    </a>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-xl">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-slate-400 text-xs uppercase tracking-wider border-b border-slate-700">
                <th class="px-6 py-3 text-left">Usuario</th>
                <th class="px-6 py-3 text-center">Nivel de Acceso</th>
                <th class="px-6 py-3 text-center">Modo Impresión</th>
                <th class="px-6 py-3 text-center">Perfil Facial</th>
                <th class="px-6 py-3 text-center">Estado</th>
                <th class="px-6 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-700">
            @foreach($usuarios as $usuario)
            <tr class="hover:bg-slate-700 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-blue-700 rounded-full flex items-center justify-center font-bold text-white uppercase">
                            {{ substr($usuario->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-medium text-white">{{ $usuario->name }}</div>
                            <div class="text-slate-500 text-xs">{{ $usuario->email }}</div>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4 text-center">
                    @php $nid = $usuario->nivel_acceso_id; @endphp
                    <span class="text-xs font-bold px-2 py-1 rounded-full
                        {{ $nid === 1 ? 'bg-emerald-900 text-emerald-300' : '' }}
                        {{ $nid === 2 ? 'bg-blue-900 text-blue-300' : '' }}
                        {{ $nid === 3 ? 'bg-red-900 text-red-300' : '' }}">
                        {{ $usuario->nivelAcceso->nombre ?? 'Sin nivel' }}
                    </span>
                </td>

                <td class="px-6 py-4 text-center">
                    @if($usuario->modo_impresion === 'color')
                        <span class="bg-yellow-900 text-yellow-300 text-xs font-bold px-2 py-1 rounded-full">🎨 Color</span>
                    @else
                        <span class="bg-slate-700 text-slate-300 text-xs font-bold px-2 py-1 rounded-full">⬛ B&N</span>
                    @endif
                </td>

                <td class="px-6 py-4 text-center">
                    @if($usuario->ruta_modelo_facial)
                        <span class="text-emerald-400 text-xs font-medium">✓ Registrado</span>
                    @else
                        <span class="text-red-400 text-xs font-medium">✗ Sin perfil</span>
                    @endif
                </td>

                <td class="px-6 py-4 text-center">
                    @if($usuario->activo)
                        <span class="bg-emerald-900 text-emerald-300 text-xs font-bold px-2 py-1 rounded-full">Activo</span>
                    @else
                        <span class="bg-slate-700 text-slate-400 text-xs font-bold px-2 py-1 rounded-full">Inactivo</span>
                    @endif
                </td>

                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('usuarios.edit', $usuario->id) }}"
                           class="text-blue-400 hover:text-blue-300 text-xs font-medium transition">
                            Editar
                        </a>
                        <span class="text-slate-600">|</span>
                        <form method="POST" action="{{ route('usuarios.toggle', $usuario->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-slate-400 hover:text-yellow-400 text-xs transition">
                                {{ $usuario->activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection