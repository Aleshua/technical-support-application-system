<?php

namespace App\Http\Middleware;

use Closure;

use App\Http\ApiMessages;
use App\Http\ApiResponses;

use Illuminate\Http\Request;

class RoleMiddleware
{
    private const rolesHierarchy = [
        'user' => 1,
        'operator' => 2,
        'admin' => 3,
    ];

    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();

        if (!$user || self::rolesHierarchy[$user->role] < self::rolesHierarchy[$role]) {
            return ApiResponses::message(ApiMessages::FORBIDDEN, 403);
        }

        return $next($request);
    }
}
