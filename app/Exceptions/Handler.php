<?php

/**
 * 全局异常处理器
 *
 * @package     App\Exceptions
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        BusinessException::class => LogLevel::NOTICE,
        SystemException::class => LogLevel::ERROR,
    ];

    public function render($request, Throwable $e): Response
    {
        if ($e instanceof BusinessException) {
            return $this->renderBusiness($request, $e);
        }

        if ($e instanceof SystemException) {
            return $this->renderSystem($request, $e);
        }

        return parent::render($request, $e);
    }

    protected function shouldReturnJson($request, Throwable $e): bool
    {
        return $request->is('api/*') || $request->expectsJson() || parent::shouldReturnJson($request, $e);
    }

    protected function renderBusiness(Request $request, BusinessException $e): Response
    {
        if ($this->shouldReturnJson($request, $e)) {
            return new JsonResponse($e->toArray(), $e->status, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->view('errors.business', [
            'message' => $e->getMessage(),
            'code' => $e->errorCode,
        ], $e->status);
    }

    protected function renderSystem(Request $request, SystemException $e): Response
    {
        if ($this->shouldReturnJson($request, $e)) {
            return new JsonResponse($e->toArray(), 500, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->view('errors.500', [
            'message' => $e->publicMessage(),
            'code' => $e->errorCode,
        ], 500);
    }
}
