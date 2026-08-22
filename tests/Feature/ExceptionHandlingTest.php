<?php

/**
 * 异常处理测试
 *
 * @package     Tests\Feature
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace Tests\Feature;

use App\Exceptions\BusinessException;
use App\Exceptions\Handler;
use App\Exceptions\SystemException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Tests\TestCase;

class ExceptionHandlingTest extends TestCase
{
    public function test_handler_is_bound(): void
    {
        $this->assertInstanceOf(Handler::class, app(ExceptionHandler::class));
    }

    public function test_business_exception_returns_json_for_api(): void
    {
        $handler = app(ExceptionHandler::class);
        $request = Request::create('/api/home', 'GET');
        $request->headers->set('Accept', 'application/json');

        $response = $handler->render($request, new BusinessException('库存不足', 422, 'BUSINESS_ERROR', 'stock'));

        $payload = json_decode($response->getContent(), true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame('库存不足', $payload['message']);
        $this->assertSame('BUSINESS_ERROR', $payload['code']);
        $this->assertSame(['stock' => ['库存不足']], $payload['errors']);
    }

    public function test_system_exception_returns_500_json_for_api(): void
    {
        $handler = app(ExceptionHandler::class);
        $request = Request::create('/api/home', 'GET');
        $request->headers->set('Accept', 'application/json');

        $response = $handler->render($request, new SystemException('磁盘写入失败'));

        $payload = json_decode($response->getContent(), true);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('系统繁忙，请稍后重试', $payload['message']);
        $this->assertSame('SYSTEM_ERROR', $payload['code']);
    }

    public function test_business_exception_returns_view_for_web(): void
    {
        $handler = app(ExceptionHandler::class);
        $request = Request::create('/about', 'GET');

        $response = $handler->render($request, new BusinessException('库存不足'));

        $this->assertSame(422, $response->getStatusCode());
        $this->assertStringContainsString('库存不足', $response->getContent());
    }
}
