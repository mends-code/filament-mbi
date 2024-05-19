<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckEmbeddedMode
{
    public function handle(Request $request, Closure $next)
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

        // Store the isEmbeddedMode status in the session
        session(['isEmbeddedMode' => $isEmbeddedMode]);

        return $next($request);
    }
}
