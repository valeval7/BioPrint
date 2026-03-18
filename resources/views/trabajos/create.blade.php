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

        {{-- Alerta de éxito --}}
        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 rounded-xl bg-green-500/10 border border-green-500/20 px-4 py-3 text-green-400 text-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Errores de validación --}}
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
                <input
                    type="text"
                    id="nombre_trabajo"
                    name="nombre_trabajo"
                    value="{{ old('nombre_trabajo') }}"
                    placeholder="Ej. Reporte mensual enero"
                    required
                    class="w-full bg-slate-900/60 border border-slate-600 rounded-xl px-4 py-3 text-white
                           placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50
                           focus:border-blue-500 transition"
                />
            </div>

            {{-- ID de impresora (cups_trabajo_id) --}}
            <div>
                <label class="block text-slate-300 text-sm font-medium mb-2" for="cups_trabajo_id">
                    ID de impresora
                    <span class="ml-1 text-slate-500 font-normal text-xs">(número asignado por CUPS)</span>
                </label>
                <input
                    type="number"
                    id="cups_trabajo_id"
                    name="cups_trabajo_id"
                    value="{{ old('cups_trabajo_id') }}"
                    placeholder="Ej. 5"
                    min="1"
                    required
                    class="w-full bg-slate-900/60 border border-slate-600 rounded-xl px-4 py-3 text-white
                           placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50
                           focus:border-blue-500 transition"
                />
            </div>

            {{-- Modo de impresión --}}
            <div>
                <label class="block text-slate-300 text-sm font-medium mb-3">
                    Modo de impresión
                </label>
                <div class="grid grid-cols-2 gap-3">

                    <label class="relative cursor-pointer">
                        <input type="radio" name="modo_impresion" value="bn"
                               class="peer sr-only" {{ old('modo_impresion', 'bn') === 'bn' ? 'checked' : '' }}>
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-600
                                    bg-slate-900/40 peer-checked:border-blue-500 peer-checked:bg-blue-500/10
                                    transition text-slate-400 peer-checked:text-white">
                            <div class="w-5 h-5 rounded-full border-2 border-current flex items-center justify-center shrink-0">
                                <div class="w-2 h-2 rounded-full bg-current scale-0 peer-checked:scale-100 transition"></div>
                            </div>
                            <span class="text-sm font-medium">Blanco y Negro</span>
                        </div>
                    </label>

                    <label class="relative cursor-pointer">
                        <input type="radio" name="modo_impresion" value="color"
                               class="peer sr-only" {{ old('modo_impresion') === 'color' ? 'checked' : '' }}>
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-600
                                    bg-slate-900/40 peer-checked:border-blue-500 peer-checked:bg-blue-500/10
                                    transition text-slate-400 peer-checked:text-white">
                            <div class="w-5 h-5 rounded-full border-2 border-current flex items-center justify-center shrink-0">
                                <div class="w-2 h-2 rounded-full bg-current scale-0 transition"></div>
                            </div>
                            <span class="text-sm font-medium">Color</span>
                        </div>
                    </label>

                </div>
            </div>

            {{-- Zona de arrastrar/subir archivo --}}
            <div>
                <label class="block text-slate-300 text-sm font-medium mb-2">
                    Archivo a imprimir
                </label>

                <div id="dropZone"
                     class="relative border-2 border-dashed border-slate-600 rounded-xl p-8 text-center
                            hover:border-blue-500/60 hover:bg-blue-500/5 transition cursor-pointer group">

                    <input type="file"
                           id="archivo"
                           name="archivo"
                           accept="{{ $extensionesAceptadas }}"
                           required
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                    {{-- Estado vacío --}}
                    <div id="dropContent" class="pointer-events-none">
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
                    </div>

                    {{-- Estado con archivo seleccionado --}}
                    <div id="filePreview" class="hidden pointer-events-none">
                        <svg class="w-8 h-8 text-blue-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0
                                     0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625
                                     c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75
                                     c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        <p id="fileName" class="text-white text-sm font-medium truncate px-4"></p>
                        <p id="fileSize" class="text-slate-400 text-xs mt-1"></p>
                    </div>
                </div>

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
            <a href="{{ route('dashboard') }}"
               class="text-slate-500 hover:text-slate-300 text-sm transition">
                ← Volver a la Cola de Impresión
            </a>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input        = document.getElementById('archivo');
    const dropZone     = document.getElementById('dropZone');
    const dropContent  = document.getElementById('dropContent');
    const filePreview  = document.getElementById('filePreview');
    const fileName     = document.getElementById('fileName');
    const fileSize     = document.getElementById('fileSize');
    const fileError    = document.getElementById('fileError');
    const fileErrorTxt = document.getElementById('fileErrorText');
    const submitBtn    = document.getElementById('submitBtn');
    const submitText   = document.getElementById('submitText');
    const form         = document.getElementById('uploadForm');

    const MIMES_VALIDOS = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.oasis.opendocument.text',
        'text/plain',
        'application/postscript',
    ];
    const MAX_MB = 50;

    function formatBytes(bytes) {
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function validarArchivo(file) {
        fileError.classList.add('hidden');
        if (!MIMES_VALIDOS.includes(file.type)) {
            fileErrorTxt.textContent = 'Tipo de archivo no válido para impresión.';
            fileError.classList.remove('hidden');
            return false;
        }
        if (file.size > MAX_MB * 1024 * 1024) {
            fileErrorTxt.textContent = `El archivo supera el límite de ${MAX_MB} MB.`;
            fileError.classList.remove('hidden');
            return false;
        }
        return true;
    }

    function mostrarArchivo(file) {
        if (!validarArchivo(file)) {
            input.value = '';
            dropContent.classList.remove('hidden');
            filePreview.classList.add('hidden');
            return;
        }
        fileName.textContent = file.name;
        fileSize.textContent  = formatBytes(file.size);
        dropContent.classList.add('hidden');
        filePreview.classList.remove('hidden');
    }

    input.addEventListener('change', () => {
        if (input.files[0]) mostrarArchivo(input.files[0]);
    });

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-blue-500', 'bg-blue-500/5');
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-blue-500', 'bg-blue-500/5');
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-500', 'bg-blue-500/5');
        const file = e.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            mostrarArchivo(file);
        }
    });

    form.addEventListener('submit', (e) => {
        if (!input.files[0] || !validarArchivo(input.files[0])) {
            e.preventDefault();
            return;
        }
        submitBtn.disabled = true;
        submitText.textContent = 'Subiendo...';
    });
});
</script>
@endpush
@endsection