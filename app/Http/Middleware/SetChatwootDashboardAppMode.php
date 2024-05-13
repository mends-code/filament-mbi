<?php

// app/Http/Middleware/SetChatwootDashboardAppMode.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class SetChatwootDashboardAppMode
{
    public function handle(Request $request, Closure $next): Response
    {
        $referer = $request->headers->get('referer');
        $isEmbedded = false;

        if ($referer) {
            $host = parse_url($referer, PHP_URL_HOST);
            if ($host === 'chat.mends.eu') {
                $isEmbedded = true;
            }
        }

        Session::put('ChatwootDashboardAppMode', $isEmbedded);

        return $next($request);
    }
}
