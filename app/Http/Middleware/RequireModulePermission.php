<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\PermissionResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireModulePermission
{
    public function handle(Request $request, Closure $next, string $moduleSlug, string $action = 'view'): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        // Admin bypass
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Use PermissionResolver for checking permissions
        $resolver = new PermissionResolver;

        if ($resolver->userCan($user, $moduleSlug, $action)) {
            return $next($request);
        }

        abort(403, 'No tienes permisos para acceder a este módulo.');
    }
}
