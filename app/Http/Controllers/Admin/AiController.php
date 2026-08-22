<?php

/**
 * 后台AI控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AiRequest;
use App\Services\AiChatService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiController extends Controller
{
    public function __construct(private readonly AiChatService $chats) {}

    public function chat(AiRequest $request): JsonResponse|StreamedResponse
    {
        $data = $request->validated();

        if ($request->boolean('stream', true)) {
            return $this->chats->stream($data);
        }

        return response()->json($this->chats->complete($data));
    }
}
