<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RemovePublicMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (strpos($request->getRequestUri(), '/public/') !== false) {
            $newUrl = str_replace('/public/', '/', $request->getRequestUri());
            return redirect($newUrl, 301);
        }

        return $next($request);
    }
}
