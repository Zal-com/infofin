<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContributorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return redirect('/');
        // Vérifie si l'utilisateur est authentifié et a le rôle "contributor"
        if ($request->user() && $request->user()->hasRole('contributor')) {
            return $next($request);
        }

        // Redirige ou retourne une réponse d'erreur si l'utilisateur n'est pas "contributor"
        return redirect()->back();
    }
}
