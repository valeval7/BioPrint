@extends('layouts.app')

@section('title', 'Confirmar Acceso')

@section('content')
<div class="min-h-screen bg-slate-900 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/20 mb-4">
                <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Área Restringida</h1>
            <p class="text-slate-400 text-sm mt-1">Introduce tu PIN de 4 dígitos para continuar</p>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-xl bg-red-500/10 border border-red-500/20 px-4 py-3 text-red-400 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif


        <form action="{{ route('pin.verify') }}" method="POST" id="pinForm" class="bg-slate-800/60 backdrop-blur border border-slate-700/50 rounded-2xl p-8 space-y-6 shadow-2xl">
            @csrf
            
            <input type="hidden" name="intended_url" value="{{ $intended_url }}">
            <div class="relative">
                <input type="password" id="pin_display" name="pin" 
                       maxlength="4" required readonly
                       class="w-full bg-slate-900/60 border border-slate-600 rounded-xl px-4 py-4 text-center text-3xl tracking-[1em] text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition"
                       placeholder="••••">
            </div>

            <div class="grid grid-cols-3 gap-3">
                @for ($i = 1; $i <= 9; $i++)
                    <button type="button" onclick="addNumber('{{ $i }}')" 
                            class="h-14 bg-slate-700/40 hover:bg-slate-600/60 text-white text-xl font-semibold rounded-xl border border-slate-600/50 transition active:scale-95">
                        {{ $i }}
                    </button>
                @endfor
                <button type="button" onclick="clearPin()" 
                        class="h-14 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-sm font-medium rounded-xl border border-red-500/20 transition">
                    Borrar
                </button>
                <button type="button" onclick="addNumber('0')" 
                        class="h-14 bg-slate-700/40 hover:bg-slate-600/60 text-white text-xl font-semibold rounded-xl border border-slate-600/50 transition active:scale-95">
                    0
                </button>
                <button type="submit" 
                        class="h-14 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition active:scale-95">
                    OK
                </button>
            </div>
        </form>

        <div class="text-center mt-6">
            <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-slate-300 text-sm transition">
                ← Cancelar y volver al inicio
            </a>
        </div>
    </div>
</div>

<script>
    const pinDisplay = document.getElementById('pin_display');

    function addNumber(num) {
        if (pinDisplay.value.length < 4) {
            pinDisplay.value += num;
        }
    }

    function clearPin() {
        pinDisplay.value = '';
    }

    document.addEventListener('keydown', (e) => {
        if (e.key >= '0' && e.key <= '9') {
            addNumber(e.key);
        } else if (e.key === 'Backspace') {
            pinDisplay.value = pinDisplay.value.slice(0, -1);
        } else if (e.key === 'Enter') {
            document.getElementById('pinForm').submit();
        }
    });
</script>
@endsection