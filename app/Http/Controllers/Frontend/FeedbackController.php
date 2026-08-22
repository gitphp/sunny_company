<?php

/**
 * 前台留言控制器
 *
 * @package     App\Http\Controllers\Frontend
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackRequest;
use App\Services\FeedbackService;
use Illuminate\Http\JsonResponse;

class FeedbackController extends Controller
{
    public function __construct(private readonly FeedbackService $feedbacks) {}

    public function store(FeedbackRequest $request): JsonResponse
    {
        return response()->json($this->feedbacks->create(
            $request->validated(),
            (string) $request->ip(),
        ), 201);
    }
}
