<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixBaseUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $baseUrl = $request->getBaseUrl();

        if ($baseUrl !== '' && $baseUrl !== null) {
            $requestUri = $request->getRequestUri();
            $pos = strpos($requestUri, '?');
            if ($pos !== false) {
                $requestUri = substr($requestUri, 0, $pos);
            }

            if (! str_starts_with($requestUri, $baseUrl)) {
                $reflector = new \ReflectionProperty($request, 'baseUrl');
                $reflector->setValue($request, '');

                $pathReflector = new \ReflectionProperty($request, 'pathInfo');
                $pathReflector->setValue($request, null);

                $request->getPathInfo();
            }
        }

        return $next($request);
    }
}
