<?php

/**
 * 权限校验中间件
 *
 * @package     App\Http\Middleware
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

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
