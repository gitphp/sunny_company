<?php

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
