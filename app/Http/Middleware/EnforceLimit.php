<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnforceLimit
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $limit = $request->query('limit', 20);

        if (is_numeric($limit) && $limit > 100) {
            $request->merge(['limit' => 100]);
        }

        return $next($request);
    }
}
