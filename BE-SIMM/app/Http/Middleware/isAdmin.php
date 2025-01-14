<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        $userAdmin = Role::where('nama_role', 'Admin')->first();

        if($user && $user->id_role === $userAdmin->id){
            return $next($request);
        }

        return response()->json([
            "message" => "Restricted Page for Admin"
        ], 401);
    }
}
