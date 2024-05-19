<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmbeddedMode
{
    public function handle(Request $request, Closure $next): Response
    {
        $embeddedHostUrl = config('chatwoot.embedded_host_url');
        $referer = $request->headers->get('referer');
        $origin = $request->headers->get('origin');

        $isEmbeddedMode = false;

        if ($embeddedHostUrl) {
            if ($referer && strpos($referer, $embeddedHostUrl) === 0) {
                $isEmbeddedMode = true;
            } elseif ($origin && strpos($origin, $embeddedHostUrl) === 0) {
                $isEmbeddedMode = true;
            }
        }

        // Add the isEmbeddedMode status to the request attributes
        $request->attributes->set('isEmbeddedMode', $isEmbeddedMode);

        // Set a cookie with the isEmbeddedMode status
        $response = $next($request);
        $response->headers->setCookie(cookie('isEmbeddedMode', $isEmbeddedMode, 0, '/', null, false, false));

        return $response;
    }
}
