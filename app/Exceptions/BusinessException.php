<?php

/**
 * 业务异常
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

class BusinessException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $status = 422,
        public readonly string $errorCode = 'BUSINESS_ERROR',
        public readonly ?string $field = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $status, $previous);
    }

    /**
     * @return never
     */
    public static function fail(string $message, ?string $field = null, int $status = 422, string $errorCode = 'BUSINESS_ERROR'): never
    {
        throw new self($message, $status, $errorCode, $field);
    }

    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return array_filter([
            'error_code' => $this->errorCode,
            'field' => $this->field,
            'status' => $this->status,
        ], fn (mixed $value): bool => $value !== null && $value !== '');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'message' => $this->getMessage(),
            'code' => $this->errorCode,
        ];

        if ($this->field !== null && $this->field !== '') {
            $payload['errors'] = [
                $this->field => [$this->getMessage()],
            ];
        }

        return $payload;
    }
}
