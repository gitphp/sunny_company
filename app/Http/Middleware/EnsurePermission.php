<?php

namespace App\Http\Middleware;

use App\Services\RbacService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function __construct(private readonly RbacService $rbac) {}

    public function handle(Request $request, Closure $next, string ...$parts): Response
    {
        $permission = implode(':', $parts);
        $user = $request->user();

        if (! $user || ! $this->rbac->userCan($user, $permission)) {
            abort(403, '没有访问权限');
        }

        return $next($request);
    }
}
