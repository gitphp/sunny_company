<?php

namespace App\Http\Controllers;

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
