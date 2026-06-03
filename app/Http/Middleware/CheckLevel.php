<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;

class CheckLevel
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if (!in_array($user->level, $roles)) {
            return ApiResponse::forbidden('No tienes permisos para realizar esta acción');
        }

        return $next($request);
    }
}
