<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TrabajoImpresionController extends Controller
{
    /**
     * Tipos de archivo aceptados para impresión.
     * Extensiones + MIME types válidos.
     */
    private array $tiposAceptados = [
        'pdf'  => 'application/pdf',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'odt'  => 'application/vnd.oasis.opendocument.text',
        'txt'  => 'text/plain',
        'ps'   => 'application/postscript',
    ];

    /**
     * Mostrar el formulario de subida.
     */
    public function create()
    {
        // Obtener impresoras disponibles desde la tabla cups_trabajos (o equivalente)
        $impresoras = DB::table('cups_trabajos')->select('id', 'nombre')->get();

        return view('trabajos.create', [
            'impresoras'      => $impresoras,
            'extensionesAceptadas' => implode(',', array_map(fn($e) => '.' . $e, array_keys($this->tiposAceptados))),
        ]);
    }

    /**
     * Procesar la subida del archivo y registrar el trabajo de impresión.
     */
    public function store(Request $request)
    {
        // --- Validación ---
        $extensiones = implode(',', array_keys($this->tiposAceptados));

        $request->validate([
            'archivo'       => [
                'required',
                'file',
                'max:51200', // 50 MB máximo
                'mimes:' . $extensiones,
            ],
            'nombre_trabajo' => ['required', 'string', 'max:255'],
            'cups_trabajo_id'=> ['required', 'integer', 'exists:cups_trabajos,id'],
            'modo_impresion' => ['required', 'in:bn,color'],
        ], [
            'archivo.mimes' => 'Solo se aceptan archivos: PDF, JPG, PNG, DOCX, ODT, TXT, PS.',
            'archivo.max'   => 'El archivo no debe superar los 50 MB.',
        ]);

        // --- Validación extra: MIME real del archivo (no solo extensión) ---
        $archivo   = $request->file('archivo');
        $mimeReal  = $archivo->getMimeType();
        $mimesValidos = array_unique(array_values($this->tiposAceptados));

        if (!in_array($mimeReal, $mimesValidos)) {
            return back()
                ->withErrors(['archivo' => 'El tipo de archivo no es válido para impresión.'])
                ->withInput();
        }

        // --- Guardar archivo cifrado (nombre aleatorio, sin revelar extensión original) ---
        $nombreCifrado = Str::uuid() . '.enc';
        $rutaRelativa  = 'trabajos/' . $nombreCifrado;

        // Guardamos el archivo en storage/app/private/trabajos/
        // En producción, aquí iría el cifrado real (OpenSSL, etc.)
        Storage::disk('local')->putFileAs('trabajos', $archivo, $nombreCifrado);

        $rutaAbsoluta = storage_path('app/trabajos/' . $nombreCifrado);

        // --- Contar páginas (estimación simple para imágenes/txt; PDF requiere librería) ---
        $paginas = $this->estimarPaginas($archivo);

        // --- Insertar en la base de datos ---
        DB::table('trabajo_impresions')->insert([
            'usuario_id'            => Auth::id(),
            'nombre_trabajo'        => $request->nombre_trabajo,
            'cups_trabajo_id'       => $request->cups_trabajo_id,
            'ruta_archivo_cifrado'  => $rutaAbsoluta,
            'modo_impresion'        => $request->modo_impresion,
            'estado'                => 'pendiente',
            'paginas'               => $paginas,
            'creado_en'             => now(),
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', "✅ Trabajo '{$request->nombre_trabajo}' agregado a la cola de impresión.");
    }

    /**
     * Estimación simple de páginas según tipo de archivo.
     * Para PDFs reales, integrar smalot/pdfparser o spatie/pdf-to-text.
     */
    private function estimarPaginas(\Illuminate\Http\UploadedFile $archivo): int
    {
        $extension = strtolower($archivo->getClientOriginalExtension());

        if ($extension === 'pdf') {
            // Intento básico con grep (Linux); fallback a 1
            $ruta = $archivo->getRealPath();
            $output = shell_exec("grep -a '/Type /Page[^s]' \"$ruta\" | wc -l 2>/dev/null");
            $paginas = (int) trim($output ?? '1');
            return max(1, $paginas);
        }

        // Imágenes y otros: 1 página por archivo
        return 1;
    }
}