<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleCategoryRequest;
use App\Services\ArticleCategoryService;
use Illuminate\Http\JsonResponse;

class ArticleCategoryController extends Controller
{
    public function __construct(private readonly ArticleCategoryService $categories) {}

    public function index(ArticleCategoryRequest $request): JsonResponse
    {
        return response()->json($this->categories->tree($request->validated()));
    }

    public function store(ArticleCategoryRequest $request): JsonResponse
    {
        return response()->json($this->categories->create($request->validated()), 201);
    }

    public function update(ArticleCategoryRequest $request): JsonResponse
    {
        return response()->json($this->categories->update(
            $request->routeId('category'),
            $request->validated(),
        ));
    }

    public function destroy(ArticleCategoryRequest $request): JsonResponse
    {
        return response()->json($this->categories->delete($request->routeId('category')));
    }
}
