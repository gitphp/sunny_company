<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthMenu;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'menus' => AuthMenu::buildTree(AuthMenu::ordered()),
        ]);
    }
}
