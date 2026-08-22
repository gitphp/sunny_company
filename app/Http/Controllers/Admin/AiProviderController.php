<?php

/**
 * 后台AI模型控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AiProviderRequest;
use App\Services\AiChatService;
use App\Services\AiProviderService;
use Illuminate\Http\JsonResponse;

class AiProviderController extends Controller
{
    public function __construct(
        private readonly AiProviderService $providers,
        private readonly AiChatService $chats,
    ) {}

    public function index(AiProviderRequest $request): JsonResponse
    {
        return response()->json($this->providers->paginate($request->validated()));
    }

    public function options(AiProviderRequest $request): JsonResponse
    {
        return response()->json($this->providers->options());
    }

    public function store(AiProviderRequest $request): JsonResponse
    {
        return response()->json($this->providers->create($request->validated()), 201);
    }

    public function update(AiProviderRequest $request): JsonResponse
    {
        return response()->json($this->providers->update($request->routeId('provider'), $request->validated()));
    }

    public function destroy(AiProviderRequest $request): JsonResponse
    {
        return response()->json($this->providers->delete($request->routeId('provider')));
    }

    public function changeStatus(AiProviderRequest $request): JsonResponse
    {
        return response()->json($this->providers->changeStatus(
            $request->routeId('provider'),
            (int) $request->validated('status'),
        ));
    }

    public function setDefault(AiProviderRequest $request): JsonResponse
    {
        return response()->json($this->providers->setDefault($request->routeId('provider')));
    }

    public function test(AiProviderRequest $request): JsonResponse
    {
        return response()->json($this->chats->test($request->routeId('provider')));
    }
}
