<?php

/**
 * 后台文件上传控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Admin;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UploadRequest;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;

class UploadController extends Controller
{
    public function __construct(private readonly UploadService $uploads) {}

    public function store(UploadRequest $request): JsonResponse
    {
        $file = $request->file('file');

        if (! $file instanceof UploadedFile) {
            BusinessException::fail('请选择文件', 'file');
        }

        return response()->json($this->uploads->store(
            $file,
            (string) ($request->validated('scene') ?: 'products'),
        ), 201);
    }
}
