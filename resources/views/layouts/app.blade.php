<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BioPrint-Secure</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen font-sans">

    <nav class="bg-slate-800 border-b border-blue-700 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-blue-600 rounded-lg p-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-blue-400 font-bold text-lg tracking-wide">BioPrint</span>
                    <span class="text-white font-bold text-lg">-Secure</span>
                </div>
            </div>

            {{-- Nav links --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('trabajos.create') }}"
                   class="text-slate-300 hover:text-blue-400 transition text-sm font-medium">
                    Subir Archivos
                </a>

                <a href="{{ route('dashboard') }}"
                   class="text-slate-300 hover:text-blue-400 transition text-sm font-medium">
                    Cola de Impresión
                </a>

                @if(auth()->user()->nivel_acceso_id === \App\Models\NivelAcceso::PREMIUM)
                    <a href="{{ route('usuarios.index') }}"
                       class="text-slate-300 hover:text-blue-400 transition text-sm font-medium">
                        Usuarios
                    </a>
                    <a href="{{ route('auditoria.index') }}"
                       class="text-slate-300 hover:text-blue-400 transition text-sm font-medium">
                        Auditoría
                    </a>
                @endif


                {{-- Separador --}}
                <div class="w-px h-5 bg-slate-600"></div>

                {{-- Badge nivel --}}
                @php $nivel = auth()->user()->nivel_acceso_id; @endphp
                <span class="text-xs font-bold px-2 py-1 rounded-full
                    {{ $nivel === 1 ? 'bg-emerald-700 text-emerald-200' : '' }}
                    {{ $nivel === 2 ? 'bg-blue-700 text-blue-200' : '' }}
                    {{ $nivel === 3 ? 'bg-red-800 text-red-200' : '' }}">
                    Nivel {{ $nivel }}
                </span>

                {{-- Avatar y nombre --}}
                <div class="flex items-center gap-2 text-sm text-slate-300">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center
                                justify-center font-bold text-white uppercase text-xs">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <span>{{ auth()->user()->name }}</span>
                </div>

                {{-- Cerrar sesión --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-1 bg-red-900 hover:bg-red-700
                                   text-red-300 hover:text-white text-xs font-semibold
                                   px-3 py-1.5 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Salir
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @if(session('success'))
            <div class="mb-4 bg-emerald-800 border border-emerald-600 text-emerald-200
                        rounded-lg px-4 py-3 text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-900 border border-red-600 text-red-200
                        rounded-lg px-4 py-3 text-sm">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>