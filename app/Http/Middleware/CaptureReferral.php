<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureReferral
{
    /**
     * URL'de ?ref=XXX varsa çerez olarak 30 gün sakla.
     * Zaten giriş yapmışsa veya çerez varsa tekrar yazmaz.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ref = $request->query('ref');

        if ($ref && !$request->cookie('referral_code') && !auth()->check()) {
            $response = $next($request);

            // 30 gün = 43200 dakika
            return $response->withCookie(
                cookie('referral_code', $ref, 43200, '/', null, false, false)
            );
        }

        return $next($request);
    }
}
