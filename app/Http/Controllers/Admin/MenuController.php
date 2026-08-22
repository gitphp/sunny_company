<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuRequest;
use App\Services\MenuService;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    public function __construct(private readonly MenuService $menus) {}

    public function index(MenuRequest $request): JsonResponse
    {
        return response()->json($this->menus->tree());
    }
}
