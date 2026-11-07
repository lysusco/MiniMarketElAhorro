<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    /**
     * Maneja una solicitud entrante y verifica en el servidor que el usuario
     * tenga uno de los roles permitidos (RF-02 y RNF-03).
     *
     * Uso: ->middleware('rol:admin') o ->middleware('rol:admin,empleado').
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Acepta tanto 'rol:admin' como 'rol:admin,empleado'.
        $roles = collect($roles)
            ->flatMap(fn (string $rol) => explode(',', $rol))
            ->all();

        $user = $request->user();

        if (! $user || ! $user->activo) {
            abort(403, 'Tu cuenta está inactiva. Contacta al administrador.');
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'No tienes permisos para realizar esta operación.');
        }

        return $next($request);
    }
}
