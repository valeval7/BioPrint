<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ColaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\TrabajoImpresionController;

// Raíz
Route::get('/', function () {
    return redirect()->route('login');
});

// Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);
    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }
    return back()->withErrors([
        'email' => 'Las credenciales no son correctas.',
    ])->onlyInput('email');
})->middleware('guest');

// Logout
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

// Dashboard y cola
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ColaController::class, 'index'])->name('dashboard');
    Route::patch('/cola/{trabajo}/liberar', [ColaController::class, 'liberar'])->name('cola.liberar');
});


// Solo admin
Route::middleware(['auth', 'solo.admin'])->group(function () {
    Route::resource('usuarios', UsuarioController::class);
    Route::patch('usuarios/{usuario}/toggle', [UsuarioController::class, 'toggle'])->name('usuarios.toggle');
    Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
});

Route::get('/subir-archivo', [TrabajoImpresionController::class, 'create'])->name('trabajos.create');
Route::post('/subir-archivo', [TrabajoImpresionController::class, 'store'])->name('trabajos.store');