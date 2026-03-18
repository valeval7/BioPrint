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
     * Solo usa trabajo_impresions — sin consultas a otras tablas.
     */
    public function create()
    {
        return view('trabajos.create', [
            'extensionesAceptadas' => implode(',', array_map(
                fn($e) => '.' . $e,
                array_keys($this->tiposAceptados)
            )),
        ]);
    }

   
    public function store(Request $request)
    {
        // --- Validación ---
        $extensiones = implode(',', array_keys($this->tiposAceptados));

        $request->validate([
            'archivo'        => [
                'required',
                'file',
                'max:51200',          // 50 MB
                'mimes:' . $extensiones,
            ],
            'nombre_trabajo'  => ['required', 'string', 'max:255'],
            'cups_trabajo_id' => ['required', 'integer', 'min:1'],  // entero simple, sin FK check
            'modo_impresion'  => ['required', 'in:bn,color'],
        ], [
            'archivo.mimes'           => 'Solo se aceptan: PDF, JPG, PNG, DOCX, ODT, TXT, PS.',
            'archivo.max'             => 'El archivo no debe superar 50 MB.',
            'cups_trabajo_id.required'=> 'Indica el ID de impresora.',
        ]);

        // --- Validación extra: MIME real (no solo extensión) ---
        $archivo      = $request->file('archivo');
        $mimeReal     = $archivo->getMimeType();
        $mimesValidos = array_unique(array_values($this->tiposAceptados));

        if (!in_array($mimeReal, $mimesValidos)) {
            return back()
                ->withErrors(['archivo' => 'El tipo de archivo no es válido para impresión.'])
                ->withInput();
        }

        // --- Guardar archivo con nombre UUID (sin revelar extensión original) ---
        $nombreCifrado = Str::uuid() . '.enc';
        Storage::disk('local')->putFileAs('trabajos', $archivo, $nombreCifrado);
        $rutaAbsoluta  = storage_path('app/trabajos/' . $nombreCifrado);

        // --- Estimar páginas ---
        $paginas = $this->estimarPaginas($archivo);

        // --- INSERT solo en trabajo_impresions ---
        DB::table('trabajo_impresions')->insert([
            'usuario_id'           => Auth::id(),
            'nombre_trabajo'       => $request->nombre_trabajo,
            'cups_trabajo_id'      => (int) $request->cups_trabajo_id,
            'ruta_archivo_cifrado' => $rutaAbsoluta,
            'modo_impresion'       => $request->modo_impresion,
            'estado'               => 'pendiente',
            'paginas'              => $paginas,
            'creado_en'            => now(),
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', "Trabajo '{$request->nombre_trabajo}' agregado a la cola de impresion.");
    }

    /**
     * Estimación de páginas.
     * PDF: grep básico en Linux. Imágenes/otros: siempre 1.
     */
    private function estimarPaginas(\Illuminate\Http\UploadedFile $archivo): int
    {
        if (strtolower($archivo->getClientOriginalExtension()) === 'pdf') {
            $ruta    = $archivo->getRealPath();
            $output  = shell_exec("grep -a '/Type /Page[^s]' \"$ruta\" | wc -l 2>/dev/null");
            $paginas = (int) trim($output ?? '1');
            return max(1, $paginas);
        }

        return 1;
    }
}