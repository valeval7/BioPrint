@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-white">Cola de Impresión</h1>
        <p class="text-slate-400 text-sm mt-1">
            Bienvenido, <span class="text-blue-400 font-medium">{{ auth()->user()->name }}</span>
            —
            @if(auth()->user()->puedeImprimirColor())
                <span class="text-yellow-400">🎨 Impresión a color habilitada</span>
            @else
                <span class="text-slate-400">⬛ Impresión en blanco y negro</span>
            @endif
        </p>
    </div>

    @if(auth()->user()->nivel_acceso_id === \App\Models\NivelAcceso::PREMIUM)
    <div class="flex gap-4">
        <div class="bg-slate-800 border border-slate-700 rounded-xl px-5 py-3 text-center">
            <p class="text-2xl font-bold text-blue-400">{{ $totalPendientes }}</p>
            <p class="text-xs text-slate-400">En cola global</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl px-5 py-3 text-center">
            <p class="text-2xl font-bold text-emerald-400">{{ $totalHoy }}</p>
            <p class="text-xs text-slate-400">Impresos hoy</p>
        </div>
    </div>
    @endif
</div>

<div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-xl">

    <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-slate-300 uppercase tracking-wider">
            @if(auth()->user()->nivel_acceso_id === \App\Models\NivelAcceso::PREMIUM)
                Todos los trabajos en cola
            @else
                Mis documentos pendientes
            @endif
        </h2>
        <span class="bg-blue-900 text-blue-300 text-xs font-bold px-3 py-1 rounded-full">
            {{ $trabajos->count() }} trabajo(s)
        </span>
    </div>

    @if($trabajos->isEmpty())
        <div class="py-16 text-center">
            <div class="text-slate-600 text-5xl mb-4">🖨️</div>
            <p class="text-slate-400 font-medium">No hay documentos en cola</p>
            <p class="text-slate-600 text-sm mt-1">Los documentos enviados desde la red aparecerán aquí</p>
        </div>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="text-slate-400 text-xs uppercase tracking-wider border-b border-slate-700">
                    <th class="px-6 py-3 text-left">Documento</th>
                    @if(auth()->user()->nivel_acceso_id === \App\Models\NivelAcceso::PREMIUM)
                        <th class="px-6 py-3 text-left">Usuario</th>
                    @endif
                    <th class="px-6 py-3 text-center">Páginas</th>
                    <th class="px-6 py-3 text-center">Modo</th>
                    <th class="px-6 py-3 text-center">Estado</th>
                    <th class="px-6 py-3 text-center">Recibido</th>
                    <th class="px-6 py-3 text-center">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @foreach($trabajos as $trabajo)
                <tr class="hover:bg-slate-700 transition">
                    <td class="px-6 py-4">
                        <div class="font-medium text-white">{{ $trabajo->nombre_trabajo }}</div>
                        <div class="text-slate-500 text-xs">#{{ $trabajo->id }}</div>
                    </td>

                    @if(auth()->user()->nivel_acceso_id === \App\Models\NivelAcceso::PREMIUM)
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-blue-700 rounded-full flex items-center justify-center text-xs font-bold text-white uppercase">
                                {{ substr($trabajo->usuario->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="text-white text-xs font-medium">{{ $trabajo->usuario->name }}</div>
                                <div class="text-slate-500 text-xs">Nivel {{ $trabajo->usuario->nivel_acceso_id }}</div>
                            </div>
                        </div>
                    </td>
                    @endif

                    <td class="px-6 py-4 text-center text-slate-300">{{ $trabajo->paginas }}</td>

                    <td class="px-6 py-4 text-center">
                        @if($trabajo->modo_impresion === 'color')
                            <span class="bg-yellow-900 text-yellow-300 text-xs font-bold px-2 py-1 rounded-full">🎨 Color</span>
                        @else
                            <span class="bg-slate-700 text-slate-300 text-xs font-bold px-2 py-1 rounded-full">⬛ B&N</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-center">
                        @if($trabajo->estado === 'pendiente')
                            <span class="bg-blue-900 text-blue-300 text-xs font-bold px-2 py-1 rounded-full">● Pendiente</span>
                        @elseif($trabajo->estado === 'liberado')
                            <span class="bg-emerald-900 text-emerald-300 text-xs font-bold px-2 py-1 rounded-full">✓ Liberado</span>
                        @elseif($trabajo->estado === 'error')
                            <span class="bg-red-900 text-red-300 text-xs font-bold px-2 py-1 rounded-full">✗ Error</span>
                        @else
                            <span class="bg-slate-700 text-slate-400 text-xs font-bold px-2 py-1 rounded-full">Cancelado</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-center text-slate-400 text-xs">
                        {{ $trabajo->creado_en->format('d/m H:i') }}
                    </td>

                    <td class="px-6 py-4 text-center">
                        @if($trabajo->estado === 'pendiente')
                            <form method="POST" action="{{ route('cola.liberar', $trabajo->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                    Liberar
                                </button>
                            </form>
                        @else
                            <span class="text-slate-600 text-xs">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection