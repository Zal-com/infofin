<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Contributor
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->hasAnyRole(['contributor', 'admin'])) {
            return $next($request);
        }
        Notification::make()->danger()->title("Vous n'êtes pas autorisé à faire cela.")->icon('heroicon-o-x-circle')->iconColor('danger');
        return redirect()->route('projects.index');
    }
}
