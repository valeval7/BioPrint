@extends('layouts.app')

@section('content')

<div class="mb-8">
    <h1 class="text-2xl font-bold text-white">Log de Auditoría</h1>
    <p class="text-slate-400 text-sm mt-1">Registro completo de eventos del sistema</p>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-xl p-4 mb-6">
    <form method="GET" action="{{ route('auditoria.index') }}" class="flex gap-3">
        <select name="tipo_evento"
                class="bg-slate-900 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
            <option value="">Todos los eventos</option>
            <option value="auth_exitosa"     {{ request('tipo_evento') === 'auth_exitosa'     ? 'selected' : '' }}>Auth exitosa</option>
            <option value="auth_fallida"     {{ request('tipo_evento') === 'auth_fallida'     ? 'selected' : '' }}>Auth fallida</option>
            <option value="trabajo_liberado" {{ request('tipo_evento') === 'trabajo_liberado' ? 'selected' : '' }}>Trabajo liberado</option>
            <option value="inicio_sesion"    {{ request('tipo_evento') === 'inicio_sesion'    ? 'selected' : '' }}>Inicio sesión</option>
            <option value="cambio_acl"       {{ request('tipo_evento') === 'cambio_acl'       ? 'selected' : '' }}>Cambio ACL</option>
        </select>
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            Filtrar
        </button>
        <a href="{{ route('auditoria.index') }}"
           class="text-slate-400 hover:text-white text-sm py-2 px-3 transition">
            Limpiar
        </a>
    </form>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-xl">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-slate-400 text-xs uppercase tracking-wider border-b border-slate-700">
                <th class="px-6 py-3 text-left">Evento</th>
                <th class="px-6 py-3 text-left">Usuario</th>
                <th class="px-6 py-3 text-left">Trabajo</th>
                <th class="px-6 py-3 text-center">IP</th>
                <th class="px-6 py-3 text-center">Fecha y Hora</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-700">
            @forelse($registros as $registro)
            <tr class="hover:bg-slate-700 transition">
                <td class="px-6 py-4">
                    @php
                        $colores = [
                            'auth_exitosa'      => 'bg-emerald-900 text-emerald-300',
                            'auth_fallida'      => 'bg-red-900 text-red-300',
                            'trabajo_liberado'  => 'bg-blue-900 text-blue-300',
                            'trabajo_cancelado' => 'bg-slate-700 text-slate-400',
                            'inicio_sesion'     => 'bg-indigo-900 text-indigo-300',
                            'cierre_sesion'     => 'bg-slate-700 text-slate-400',
                            'cambio_acl'        => 'bg-yellow-900 text-yellow-300',
                        ];
                        $clase = $colores[$registro->tipo_evento] ?? 'bg-slate-700 text-slate-400';
                    @endphp
                    <span class="text-xs font-bold px-2 py-1 rounded-full {{ $clase }}">
                        {{ $registro->tipo_evento }}
                    </span>
                </td>

                <td class="px-6 py-4">
                    @if($registro->usuario)
                        <div class="font-medium text-white text-xs">{{ $registro->usuario->name }}</div>
                        <div class="text-slate-500 text-xs">Niv. {{ $registro->usuario->nivel_acceso_id }}</div>
                    @else
                        <span class="text-slate-600 text-xs">Sistema</span>
                    @endif
                </td>

                <td class="px-6 py-4">
                    @if($registro->trabajo)
                        <div class="text-slate-300 text-xs">{{ $registro->trabajo->nombre_trabajo }}</div>
                        <div class="text-slate-500 text-xs">
                            {{ $registro->trabajo->modo_impresion === 'color' ? '🎨 Color' : '⬛ B&N' }}
                            · {{ $registro->trabajo->paginas }} pág.
                        </div>
                    @else
                        <span class="text-slate-600 text-xs">—</span>
                    @endif
                </td>

                <td class="px-6 py-4 text-center text-slate-400 text-xs font-mono">
                    {{ $registro->direccion_ip }}
                </td>

                <td class="px-6 py-4 text-center text-slate-400 text-xs">
                    {{ $registro->creado_en->format('d/m/Y H:i:s') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-12 text-center text-slate-500">
                    No hay registros de auditoría
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($registros->hasPages())
    <div class="px-6 py-4 border-t border-slate-700">
        {{ $registros->links() }}
    </div>
    @endif
</div>

@endsection