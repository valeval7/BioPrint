@extends('layouts.app')

@section('title', 'Subir Archivo para Impresión')

@section('content')
<div class="min-h-screen bg-slate-900 flex items-center justify-center px-4 py-12">
<div class="w-full max-w-xl">

    {{-- Encabezado --}}
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/20 mb-4">
            <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.75
                         19.5l.001-.001A21.998 21.998 0 0112 21c2.47 0 4.836-.413 7.002-1.172l.001.001L19.25
                         19.5l-.72-5.671M3 10.5h18M6.75 8.25h10.5a2.25 2.25 0 002.25-2.25V6A2.25 2.25 0
                         0017.25 3.75H6.75A2.25 2.25 0 004.5 6v.001a2.25 2.25 0 002.25 2.249z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Subir Archivo para Impresión</h1>
        <p class="text-slate-400 text-sm mt-1">Formatos aceptados: PDF, JPG, PNG, DOCX, ODT, TXT, PS · Máx. 50 MB</p>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-xl bg-green-500/10 border border-green-500/20 px-4 py-3 text-green-400 text-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-xl bg-red-500/10 border border-red-500/20 px-4 py-3 text-red-400 text-sm space-y-1">
            @foreach($errors->all() as $error)
                <p class="flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
                    </svg>
                    {{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    {{-- Formulario --}}
    <form action="{{ route('trabajos.store') }}"
          method="POST"
          enctype="multipart/form-data"
          id="uploadForm"
          class="bg-slate-800/60 backdrop-blur border border-slate-700/50 rounded-2xl p-8 space-y-6 shadow-2xl">
        @csrf

        {{-- Nombre del trabajo --}}
        <div>
            <label class="block text-slate-300 text-sm font-medium mb-2" for="nombre_trabajo">
                Nombre del trabajo
            </label>
            <input type="text" id="nombre_trabajo" name="nombre_trabajo"
                   value="{{ old('nombre_trabajo') }}"
                   placeholder="Ej. Reporte mensual enero" required
                   class="w-full bg-slate-900/60 border border-slate-600 rounded-xl px-4 py-3 text-white
                          placeholder-slate-500 text-sm focus:outline-none focus:ring-2
                          focus:ring-blue-500/50 focus:border-blue-500 transition"/>
        </div>

        {{-- Modo de impresión (solo PREMIUM o ESTANDAR) --}}
        @php
            $nivelId = auth()->user()->nivel_acceso_id;
            $esPremiumOEstandar = in_array($nivelId, [
                \App\Models\NivelAcceso::PREMIUM,
                \App\Models\NivelAcceso::ESTANDAR,
            ]);
        @endphp

        @if($esPremiumOEstandar)
        <div>
            <label class="block text-slate-300 text-sm font-medium mb-3">Modo de impresión</label>
            <div class="grid grid-cols-2 gap-3">
                @foreach(['bn' => 'Blanco y Negro', 'color' => 'Color'] as $valor => $etiqueta)
                <label class="relative cursor-pointer">
                    <input type="radio" name="modo_impresion" value="{{ $valor }}"
                           class="peer sr-only"
                           {{ old('modo_impresion', 'bn') === $valor ? 'checked' : '' }}>
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-600
                                bg-slate-900/40 peer-checked:border-blue-500 peer-checked:bg-blue-500/10
                                transition text-slate-400 peer-checked:text-white">
                        <div class="w-5 h-5 rounded-full border-2 border-current flex items-center justify-center shrink-0">
                            <div class="w-2 h-2 rounded-full bg-current transition-transform scale-0 peer-checked:scale-100"></div>
                        </div>
                        <span class="text-sm font-medium">{{ $etiqueta }}</span>
                    </div>
                </label>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Zona de carga --}}
        <div>
            <label class="block text-slate-300 text-sm font-medium mb-2">Archivo a imprimir</label>

            {{-- Drop zone --}}
            <div id="dropZone"
                 class="border-2 border-dashed border-slate-600 rounded-xl p-8 text-center
                        hover:border-blue-500/60 hover:bg-blue-500/5 transition group cursor-pointer">
                <label for="archivo" class="cursor-pointer block">
                    <svg class="w-10 h-10 text-slate-500 mx-auto mb-3 group-hover:text-blue-400 transition"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5
                                 m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                    <p class="text-slate-400 text-sm">
                        Arrastra tu archivo aquí o
                        <span class="text-blue-400 font-medium">selecciona uno</span>
                    </p>
                    <p class="text-slate-500 text-xs mt-1">PDF, JPG, PNG, DOCX, ODT, TXT, PS · Máx. 50 MB</p>
                </label>
                <input type="file" id="archivo" name="archivo"
                       accept=".pdf,.jpg,.jpeg,.png,.docx,.odt,.txt,.ps"
                       required
                       class="hidden">
            </div>

            {{-- Preview --}}
            <div id="filePreview" class="hidden">
                <div class="flex items-center gap-4 bg-slate-900/60 rounded-xl px-4 py-3 border border-slate-700">
                    <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-blue-500/15 border border-blue-500/30">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0
                                     0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625
                                     c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75
                                     c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p id="fileName" class="text-white text-sm font-medium truncate"></p>
                        <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                            <span id="fileSize" class="text-slate-400 text-xs"></span>
                            <span id="filePagesWrap" class="hidden text-slate-600 text-xs">·</span>
                            <span id="filePages" class="text-slate-400 text-xs"></span>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <button type="button" id="quitarArchivo"
                        class="mt-2 text-slate-500 hover:text-red-400 text-xs flex items-center gap-1 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Quitar archivo
                </button>
            </div>

            {{-- Error --}}
            <p id="fileError" class="hidden mt-2 text-red-400 text-xs flex items-center gap-1">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
                </svg>
                <span id="fileErrorText"></span>
            </p>
        </div>

        {{-- Botón enviar --}}
        <button type="submit" id="submitBtn"
                class="w-full bg-blue-600 hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed
                       text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5
                         m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
            </svg>
            <span id="submitText">Enviar a cola de impresión</span>
        </button>

    </form>

    <div class="text-center mt-5">
        <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-slate-300 text-sm transition">
            ← Volver a la Cola de Impresión
        </a>
    </div>

</div>
</div>

{{-- Script inline directo en el contenido, sin @push --}}
<script>
(function () {
    function init() {
        var input         = document.getElementById('archivo');
        var dropZone      = document.getElementById('dropZone');
        var filePreview   = document.getElementById('filePreview');
        var fileName      = document.getElementById('fileName');
        var fileSize      = document.getElementById('fileSize');
        var filePagesEl   = document.getElementById('filePages');
        var filePagesWrap = document.getElementById('filePagesWrap');
        var fileError     = document.getElementById('fileError');
        var fileErrTxt    = document.getElementById('fileErrorText');
        var quitarBtn     = document.getElementById('quitarArchivo');
        var submitBtn     = document.getElementById('submitBtn');
        var submitText    = document.getElementById('submitText');
        var form          = document.getElementById('uploadForm');

        if (!input || !dropZone) return; // seguridad

        var TIPOS_VALIDOS = [
            'application/pdf', 'image/jpeg', 'image/png',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.oasis.opendocument.text',
            'text/plain', 'application/postscript'
        ];
        var MAX_BYTES = 50 * 1024 * 1024;

        function fmtBytes(b) {
            return b < 1048576
                ? (b / 1024).toFixed(1) + ' KB'
                : (b / 1048576).toFixed(2) + ' MB';
        }

        function mostrarError(msg) {
            fileErrTxt.textContent = msg;
            fileError.classList.remove('hidden');
        }

        function limpiarError() {
            fileError.classList.add('hidden');
        }

        function validar(file) {
            limpiarError();
            var ext = file.name.split('.').pop().toLowerCase();
            var extsValidas = ['pdf','jpg','jpeg','png','docx','odt','txt','ps'];
            var mimeOk = TIPOS_VALIDOS.indexOf(file.type) !== -1;
            var extOk  = extsValidas.indexOf(ext) !== -1;
            if (!mimeOk && !extOk) { mostrarError('Tipo de archivo no permitido.'); return false; }
            if (file.size > MAX_BYTES) { mostrarError('El archivo supera el límite de 50 MB.'); return false; }
            return true;
        }

        function limpiarArchivo() {
            input.value = '';
            limpiarError();
            filePagesEl.textContent = '';
            filePagesWrap.classList.add('hidden');
            dropZone.classList.remove('hidden');
            filePreview.classList.add('hidden');
        }

        function contarPaginasPDF(file) {
            return file.arrayBuffer().then(function(buf) {
                var text = new TextDecoder('latin1').decode(buf);
                var matches = text.match(/\/Type\s*\/Page[^s]/g);
                return matches ? matches.length : null;
            }).catch(function() { return null; });
        }

        function mostrarArchivo(file) {
            if (!validar(file)) { limpiarArchivo(); return; }

            fileName.textContent = file.name;
            fileSize.textContent = fmtBytes(file.size);
            filePagesEl.textContent = '';
            filePagesWrap.classList.add('hidden');

            var esPDF = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');

            if (esPDF) {
                contarPaginasPDF(file).then(function(pages) {
                    if (pages) {
                        filePagesEl.textContent = pages + (pages === 1 ? ' página' : ' páginas');
                        filePagesWrap.classList.remove('hidden');
                    }
                });
            }

            dropZone.classList.add('hidden');
            filePreview.classList.remove('hidden');
        }

        input.addEventListener('change', function() {
            if (input.files && input.files[0]) mostrarArchivo(input.files[0]);
        });

        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('border-blue-500', 'bg-blue-500/5');
        });
        dropZone.addEventListener('dragleave', function() {
            dropZone.classList.remove('border-blue-500', 'bg-blue-500/5');
        });
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500', 'bg-blue-500/5');
            var file = e.dataTransfer.files[0];
            if (!file) return;
            var dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            mostrarArchivo(file);
        });

        quitarBtn.addEventListener('click', limpiarArchivo);

        form.addEventListener('submit', function(e) {
            if (!input.files[0] || !validar(input.files[0])) { e.preventDefault(); return; }
            submitBtn.disabled = true;
            submitText.textContent = 'Subiendo...';
        });
    }

    // Funciona tanto si el DOM ya cargó como si no
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>

@endsection