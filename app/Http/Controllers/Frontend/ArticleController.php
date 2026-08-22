<?php

/**
 * 前台文章控制器
 *
 * @package     App\Http\Controllers\Frontend
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $articles) {}

    public function index(ArticleRequest $request): JsonResponse
    {
        return response()->json($this->articles->publicPaginate($request->validated()));
    }

    public function show(ArticleRequest $request): JsonResponse
    {
        return response()->json($this->articles->publicFind($request->routeId('article')));
    }
}
