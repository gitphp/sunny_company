<?php

/**
 * 前台广告控制器
 *
 * @package     App\Http\Controllers\Frontend
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdRequest;
use App\Services\AdMaterialService;
use Illuminate\Http\JsonResponse;

class AdController extends Controller
{
    public function __construct(private readonly AdMaterialService $materials) {}

    public function show(AdRequest $request): JsonResponse
    {
        return response()->json($this->materials->activeByCode($request->route('code')));
    }
}
