@extends('layouts.app')

@section('content')

<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('usuarios.index') }}" class="text-slate-400 hover:text-blue-400 text-sm transition">
            ← Volver a usuarios
        </a>
        <h1 class="text-2xl font-bold text-white mt-2">Nuevo Usuario</h1>
    </div>

    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-8 shadow-xl">
        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-sm text-slate-400 mb-2">Nombre completo</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2.5
                              text-white text-sm focus:outline-none focus:border-blue-500
                              @error('name') border-red-500 @enderror">
                @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm text-slate-400 mb-2">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2.5
                              text-white text-sm focus:outline-none focus:border-blue-500
                              @error('email') border-red-500 @enderror">
                @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm text-slate-400 mb-2">Contraseña</label>
                <input type="password" name="password" required
                       class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2.5
                              text-white text-sm focus:outline-none focus:border-blue-500
                              @error('password') border-red-500 @enderror">
                @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm text-slate-400 mb-2">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" required
                       class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2.5
                              text-white text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm text-slate-400 mb-2">Nivel de Acceso</label>
                <select name="nivel_acceso_id" required
                        class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2.5
                               text-white text-sm focus:outline-none focus:border-blue-500
                               @error('nivel_acceso_id') border-red-500 @enderror">
                    <option value="">Seleccionar nivel...</option>
                    @foreach($niveles as $nivel)
                        <option value="{{ $nivel->id }}" {{ old('nivel_acceso_id') == $nivel->id ? 'selected' : '' }}>
                            Nivel {{ $nivel->id }} — {{ $nivel->nombre }}
                            ({{ $nivel->modo_impresion === 'color' ? '🎨 Color' : '⬛ B&N' }})
                        </option>
                    @endforeach
                </select>
                @error('nivel_acceso_id')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-slate-500 text-xs mt-2">El modo de impresión se asigna automáticamente según el nivel.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 rounded-lg transition text-sm">
                    Crear Usuario
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