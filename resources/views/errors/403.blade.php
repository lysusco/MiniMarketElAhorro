<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Acceso no autorizado</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex items-center justify-center bg-gray-100">
            <div class="max-w-md w-full bg-white shadow-sm rounded-lg p-8 text-center">
                <div class="text-6xl font-bold text-red-600">403</div>
                <h1 class="mt-4 text-xl font-semibold text-gray-800">Acceso no autorizado</h1>
                <p class="mt-2 text-sm text-gray-600">
                    No tienes permisos para realizar esta operación.
                    Si consideras que se trata de un error, contacta al administrador del sistema.
                </p>
                <a href="{{ route('dashboard') }}" class="inline-block mt-6 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                    Volver al panel principal
                </a>
            </div>
        </div>
    </body>
</html>
