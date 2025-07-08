<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ValidateImporterAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }

        $importerId = $request->route('importer');
        
        if (!$importerId) {
            abort(400, 'ID del importador no proporcionado');
        }

        // Verificar si el usuario está asociado al importador
        $hasAccess = DB::table('importer_user')
            ->where('user_id', $user->id)
            ->where('importer_id', $importerId)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'No tienes permisos para acceder a este importador');
        }

        return $next($request);
    }
} 