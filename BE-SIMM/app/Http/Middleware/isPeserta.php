<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;

class isPeserta
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        $userPeserta = Role::where('nama_role', 'Peserta')->first();

        if($user && $user->id_role === $userPeserta->id){
            return $next($request);
        }

        return response()->json([
            "message" => "Restricted Page for Peserta"
        ], 401);
    }
}
