<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function __construct(private readonly UserService $users) {}

    public function index(UserRequest $request): JsonResponse
    {
        return response()->json($this->users->paginate($request->validated(), $request->user()));
    }

    public function store(UserRequest $request): JsonResponse
    {
        return response()->json($this->users->create($request->validated(), $request->user(), [
            'ip' => (string) $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]), 201);
    }

    public function show(UserRequest $request): JsonResponse
    {
        return response()->json($this->users->find($request->routeId('user')));
    }

    public function update(UserRequest $request): JsonResponse
    {
        return response()->json($this->users->update(
            $request->routeId('user'),
            $request->validated(),
            $request->user(),
        ));
    }

    public function destroy(UserRequest $request): JsonResponse
    {
        return response()->json($this->users->delete($request->routeId('user'), $request->user()));
    }

    public function batchDestroy(UserRequest $request): JsonResponse
    {
        return response()->json($this->users->batchDelete(
            array_map('strval', $request->validated('ids')),
            $request->user(),
        ));
    }

    public function changeStatus(UserRequest $request): JsonResponse
    {
        return response()->json($this->users->changeStatus(
            $request->routeId('user'),
            (int) $request->validated('user_status'),
            $request->user(),
        ));
    }

    public function resetPassword(UserRequest $request): JsonResponse
    {
        return response()->json($this->users->resetPassword(
            $request->routeId('user'),
            (string) $request->validated('password'),
        ));
    }

    public function export(UserRequest $request): StreamedResponse
    {
        return $this->users->export($request->validated(), $request->user());
    }
}
