<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogger;
use Closure;
use Illuminate\Http\Request;

class LogApiRequests
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->user()) {
            ActivityLogger::log(
                'api.request',
                "API request to: {$request->method()} {$request->path()}",
                null,
                [
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'status_code' => $response->status(),
                ]
            );
        }

        return $response;
    }
}