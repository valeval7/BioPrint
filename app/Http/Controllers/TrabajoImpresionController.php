<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\NivelAcceso;
use App\Models\TrabajoImpresion;

class TrabajoImpresionController extends Controller
{
    private array $extensionesValidas = ['pdf', 'jpg', 'jpeg', 'png', 'docx', 'odt', 'txt', 'ps'];

    private array $mimesValidos = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.oasis.opendocument.text',
        'text/plain',
        'application/postscript',
    ];

    public function create()
    {
        return view('trabajos.create');
    }

    public function store(Request $request)
    {
        $nivelId = Auth::user()->nivel_acceso_id;
        $requiereModo = in_array($nivelId, [NivelAcceso::PREMIUM, NivelAcceso::ESTANDAR]);

        // --- Validación ---
        $rules = [
            'nombre_trabajo' => ['required', 'string', 'max:255'],
            'archivo'        => [
                'required',
                'file',
                'max:51200',
                'mimes:' . implode(',', $this->extensionesValidas),
            ],
        ];

        if ($requiereModo) {
            $rules['modo_impresion'] = ['required', 'in:bn,color'];
        }

        $request->validate($rules, [
            'archivo.mimes' => 'Solo se aceptan: PDF, JPG, PNG, DOCX, ODT, TXT, PS.',
            'archivo.max'   => 'El archivo no debe superar 50 MB.',
        ]);

        // --- Validar MIME real ---
        $archivo  = $request->file('archivo');
        $mimeReal = $archivo->getMimeType();

        if (!in_array($mimeReal, $this->mimesValidos)) {
            return back()
                ->withErrors(['archivo' => 'El tipo de archivo no es válido para impresión.'])
                ->withInput();
        }

        // --- Guardar archivo ---
        $nombreArchivo = Str::uuid() . '.' . $archivo->getClientOriginalExtension();
        $ruta = Storage::disk('local')->putFileAs('trabajos', $archivo, $nombreArchivo);

        // --- INSERT en BD ---
        DB::table('trabajo_impresions')->insert([
            'usuario_id'           => Auth::id(),
            'nombre_trabajo'       => $request->nombre_trabajo,
            'ruta_archivo_cifrado' => $ruta,
            'modo_impresion'       => $requiereModo ? $request->modo_impresion : 'bn',
            'estado'               => 'pendiente',
            'paginas'              => 1,
            'creado_en'            => now(),
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', "Trabajo '{$request->nombre_trabajo}' agregado a la cola de impresión.");
    }

    public function descargar(int $trabajo)
    {
        // Buscar el trabajo y verificar que pertenece al usuario autenticado
        $registro = DB::table('trabajo_impresions')
            ->where('id', $trabajo)
            ->where('usuario_id', Auth::id())
            ->first();

        // Si no existe o no le pertenece, 403
        abort_if(!$registro, 403, 'No tienes permiso para descargar este archivo.');

        $ruta = $registro->ruta_archivo_cifrado;

        // Verificar que el archivo existe en disco
        abort_unless(Storage::disk('local')->exists($ruta), 404, 'El archivo no se encontró en el servidor.');

        // Reconstruir el nombre original limpio desde el nombre del trabajo
        $extension  = pathinfo($ruta, PATHINFO_EXTENSION);
        $nombreBase = Str::slug($registro->nombre_trabajo, '_');
        $nombreDescarga = $nombreBase . '.' . $extension;

        return Storage::disk('local')->download($ruta, $nombreDescarga);
    }
}