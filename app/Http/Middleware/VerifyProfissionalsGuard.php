<?php

namespace App\Http\Middleware;

use App\Models\Profissional;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyProfissionalGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!auth()->user() instanceof Profissional){

            return response([
                'status' => false,
                'message' => "Não é uma intancia de Profissional"
            ],200);
        }
        return $next($request);
    }
}