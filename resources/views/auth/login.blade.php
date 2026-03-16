<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BioPrint-Secure — Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center font-sans">

    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl shadow-lg shadow-blue-900 mb-4">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">
                BioPrint<span class="text-blue-400">-Secure</span>
            </h1>
            <p class="text-slate-400 text-sm mt-1">Sistema de Liberación Biométrica de Impresión</p>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl p-8">
            <h2 class="text-lg font-semibold text-white mb-6">Acceso al Sistema</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-5">
                    <label class="block text-sm text-slate-400 mb-2">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2.5
                                  text-white text-sm placeholder-slate-500
                                  focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                  @error('email') border-red-500 @enderror"
                           placeholder="usuario@empresa.com">
                    @error('email')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm text-slate-400 mb-2">Contraseña</label>
                    <input type="password" name="password" required
                           class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2.5
                                  text-white text-sm placeholder-slate-500
                                  focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                  @error('password') border-red-500 @enderror"
                           placeholder="••••••••">
                    @error('password')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center mb-6">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 accent-blue-500">
                    <label for="remember" class="ml-2 text-sm text-slate-400">Mantener sesión iniciada</label>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold
                               py-2.5 rounded-lg transition text-sm shadow-lg shadow-blue-900">
                    Iniciar Sesión
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-700 text-center">
                <p class="text-slate-500 text-xs">
                    🔒 La liberación de documentos requiere autenticación facial en la estación de impresión
                </p>
            </div>
        </div>

        <p class="text-center text-slate-600 text-xs mt-6">
            BioPrint-Secure · Ubuntu 24.04 LTS · Tecnologías Emergentes
        </p>
    </div>
</body>
</html>