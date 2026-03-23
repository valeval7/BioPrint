@extends('layouts.app')

@section('content')

<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('usuarios.index') }}" class="text-slate-400 hover:text-blue-400 text-sm transition">
            ← Volver a usuarios
        </a>
        <h1 class="text-2xl font-bold text-white mt-2">Editar Usuario</h1>
        <p class="text-slate-400 text-sm mt-1">{{ $usuario->email }}</p>
    </div>

    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-8 shadow-xl">
        <form method="POST" action="{{ route('usuarios.update', $usuario->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label class="block text-sm text-slate-400 mb-2">Nombre completo</label>
                <input type="text" name="name" value="{{ old('name', $usuario->name) }}" required
                       class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2.5
                              text-white text-sm focus:outline-none focus:border-blue-500
                              @error('name') border-red-500 @enderror">
                @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm text-slate-400 mb-2">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                       class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2.5
                              text-white text-sm focus:outline-none focus:border-blue-500
                              @error('email') border-red-500 @enderror">
                @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm text-slate-400 mb-2">
                    Nueva contraseña
                    <span class="text-slate-500 font-normal">(dejar vacío para no cambiar)</span>
                </label>
                <input type="password" name="password"
                       class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2.5
                              text-white text-sm focus:outline-none focus:border-blue-500
                              @error('password') border-red-500 @enderror"
                       placeholder="••••••••">
                @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm text-slate-400 mb-2">Confirmar nueva contraseña</label>
                <input type="password" name="password_confirmation"
                       class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2.5
                              text-white text-sm focus:outline-none focus:border-blue-500"
                       placeholder="••••••••">
            </div>

            <div class="mb-6">
                <label class="block text-sm text-slate-400 mb-2">Nivel de Acceso</label>
                <select name="nivel_acceso_id" required
                        class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2.5
                               text-white text-sm focus:outline-none focus:border-blue-500">
                    @foreach($niveles as $nivel)
                       <option value="{{ $nivel->id }}"
    {{ old('nivel_acceso_id', $usuario->nivel_acceso_id) == $nivel->id ? 'selected' : '' }}>
    
    Nivel {{ $nivel->id }} — {{ $nivel->nombre }}
    
    (
    @if($nivel->nombre == 'Usuario')
        🎨 Color / ⬛ B&N
    @elseif($nivel->modo_impresion === 'color')
        🎨 Color
    @else
        ⬛ B&N
    @endif
    )
</option>
                    @endforeach
                </select>
                <p class="text-slate-500 text-xs mt-2">
                    Modo actual:
                    <span class="font-medium {{ $usuario->modo_impresion === 'color' ? 'text-yellow-400' : 'text-slate-300' }}">
                        {{ $usuario->modo_impresion === 'color' ? '🎨 Color' : '⬛ B&N' }}
                    </span>
                </p>
            </div>
             <div class="mb-6">
                <label class="block text-sm text-slate-400 mb-2">
                    Ruta del Modelo Facial
                    <span class="text-slate-500 font-normal">(ruta al archivo .dat de reconocimiento)</span>
                </label>
                <input type="text" name="ruta_modelo_facial"
                       value="{{ old('ruta_modelo_facial', $usuario->ruta_modelo_facial) }}"
                       class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2.5
                              text-white text-sm focus:outline-none focus:border-blue-500 font-mono
                              @error('ruta_modelo_facial') border-red-500 @enderror"
                       placeholder="/lib/security/howdy/models/vmg.dat">
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 rounded-lg transition text-sm">
                    Guardar Cambios
                </button>
                <a href="{{ route('usuarios.index') }}"
                   class="flex-1 text-center bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold py-2.5 rounded-lg transition text-sm">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@endsection