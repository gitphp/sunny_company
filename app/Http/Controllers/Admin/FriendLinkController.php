<?php

/**
 * 后台友情链接控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FriendLinkRequest;
use App\Services\FriendLinkService;
use Illuminate\Http\JsonResponse;

class FriendLinkController extends Controller
{
    public function __construct(private readonly FriendLinkService $links) {}

    public function index(FriendLinkRequest $request): JsonResponse
    {
        return response()->json($this->links->paginate($request->validated()));
    }

    public function store(FriendLinkRequest $request): JsonResponse
    {
        return response()->json($this->links->create($request->validated()), 201);
    }

    public function update(FriendLinkRequest $request): JsonResponse
    {
        return response()->json($this->links->update($request->routeId('link'), $request->validated()));
    }

    public function destroy(FriendLinkRequest $request): JsonResponse
    {
        return response()->json($this->links->delete($request->routeId('link')));
    }

    public function changeStatus(FriendLinkRequest $request): JsonResponse
    {
        return response()->json($this->links->changeStatus(
            $request->routeId('link'),
            (int) $request->validated('link_status'),
        ));
    }
}
