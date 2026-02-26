<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CreatorApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        if ($user->user_type !== 'event_creator') {
            return response()->json([
                'success' => false,
                'message' => 'User is not an event creator'
            ], 403);
        }

        if (!$user->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'Creator account not approved yet'
            ], 403);
        }

        return $next($request);
    }
}
