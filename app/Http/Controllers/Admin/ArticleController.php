<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleRequest;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $articles) {}

    public function index(ArticleRequest $request): JsonResponse
    {
        return response()->json($this->articles->paginate($request->validated()));
    }

    public function store(ArticleRequest $request): JsonResponse
    {
        return response()->json($this->articles->create($request->validated(), $request->user()), 201);
    }

    public function show(ArticleRequest $request): JsonResponse
    {
        return response()->json($this->articles->find($request->routeId('article')));
    }

    public function update(ArticleRequest $request): JsonResponse
    {
        return response()->json($this->articles->update(
            $request->routeId('article'),
            $request->validated(),
            $request->user(),
        ));
    }

    public function destroy(ArticleRequest $request): JsonResponse
    {
        return response()->json($this->articles->delete($request->routeId('article')));
    }

    public function batchDestroy(ArticleRequest $request): JsonResponse
    {
        return response()->json($this->articles->batchDelete(
            array_map('strval', $request->validated('ids')),
        ));
    }

    public function changeStatus(ArticleRequest $request): JsonResponse
    {
        return response()->json($this->articles->changeStatus(
            $request->routeId('article'),
            $request->validated(),
            $request->user(),
        ));
    }
}
