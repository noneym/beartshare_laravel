<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sonu eğik çizgili adresleri (/eserler/) çizgisiz haline 301 ile yönlendirir;
 * aynı sayfanın iki adresten indekslenmesini önler. Kök (/) ve GET dışı istekler dokunulmaz.
 */
class RedirectTrailingSlash
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->getPathInfo();

        if ($path !== '/' && str_ends_with($path, '/') && $request->isMethod('GET')) {
            $target = rtrim($path, '/');
            $query = $request->getQueryString();

            return redirect($target . ($query ? '?' . $query : ''), 301);
        }

        return $next($request);
    }
}
