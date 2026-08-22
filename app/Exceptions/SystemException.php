<?php

/**
 * 系统异常
 *
 * @package     App\Exceptions
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class SystemException extends RuntimeException
{
    public function __construct(
        string $message = '系统繁忙，请稍后重试',
        ?Throwable $previous = null,
        public readonly string $errorCode = 'SYSTEM_ERROR',
    ) {
        parent::__construct($message, 500, $previous);
    }

    /**
     * @return never
     */
    public static function fail(string $message = '系统繁忙，请稍后重试', ?Throwable $previous = null): never
    {
        throw new self($message, $previous);
    }

    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return [
            'error_code' => $this->errorCode,
        ];
    }

    public function publicMessage(): string
    {
        return '系统繁忙，请稍后重试';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'message' => $this->publicMessage(),
            'code' => $this->errorCode,
        ];

        if (config('app.debug')) {
            $payload['debug'] = $this->getMessage();

            if ($this->getPrevious()) {
                $payload['debug_previous'] = $this->getPrevious()->getMessage();
            }
        }

        return $payload;
    }
}
