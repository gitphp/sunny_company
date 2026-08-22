<?php

/**
 * 后台岗位控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function __construct(private readonly PostService $posts) {}

    public function index(PostRequest $request): JsonResponse
    {
        return response()->json($this->posts->tree());
    }

    public function store(PostRequest $request): JsonResponse
    {
        return response()->json($this->posts->create(
            $request->validated(),
            (string) ($request->user()?->id ?? 0),
        ), 201);
    }

    public function update(PostRequest $request): JsonResponse
    {
        return response()->json($this->posts->update(
            $request->routeId('post'),
            $request->validated(),
        ));
    }

    public function destroy(PostRequest $request): JsonResponse
    {
        return response()->json($this->posts->delete($request->routeId('post')));
    }
}
