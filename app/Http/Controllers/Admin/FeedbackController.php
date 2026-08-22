<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FeedbackRequest;
use App\Services\FeedbackService;
use Illuminate\Http\JsonResponse;

class FeedbackController extends Controller
{
    public function __construct(private readonly FeedbackService $feedbacks) {}

    public function index(FeedbackRequest $request): JsonResponse
    {
        return response()->json($this->feedbacks->paginate($request->validated()));
    }

    public function show(FeedbackRequest $request): JsonResponse
    {
        return response()->json($this->feedbacks->find($request->routeId('feedback')));
    }

    public function reply(FeedbackRequest $request): JsonResponse
    {
        return response()->json($this->feedbacks->reply(
            $request->routeId('feedback'),
            $request->validated(),
        ));
    }

    public function changeStatus(FeedbackRequest $request): JsonResponse
    {
        return response()->json($this->feedbacks->changeStatus(
            $request->routeId('feedback'),
            (int) $request->validated('fb_status'),
        ));
    }

    public function destroy(FeedbackRequest $request): JsonResponse
    {
        return response()->json($this->feedbacks->delete($request->routeId('feedback')));
    }

    public function batchDestroy(FeedbackRequest $request): JsonResponse
    {
        return response()->json($this->feedbacks->batchDelete(
            array_map('strval', $request->validated('ids')),
        ));
    }
}
