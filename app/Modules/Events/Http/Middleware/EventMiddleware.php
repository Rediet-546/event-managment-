<?php

namespace App\Modules\Events\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EventMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if event module is enabled
        if (!config('modules.events.enabled', true)) {
            abort(404, 'Events module is disabled.');
        }

        // Add module-specific headers
        $response = $next($request);
        
        if (method_exists($response, 'header')) {
            $response->header('X-Module', 'Events');
        }

        return $response;
    }
}