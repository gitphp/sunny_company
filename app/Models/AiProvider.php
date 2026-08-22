<?php

/**
 * AI模型
 *
 * @package     App\Models
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Models;

use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'provider_name',
    'driver',
    'base_url',
    'api_key',
    'model',
    'temperature',
    'max_tokens',
    'system_prompt',
    'is_default',
    'status',
    'sort_order',
])]
#[Hidden(['api_key'])]
class AiProvider extends Model
{
    protected $table = 'ai_provider';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (AiProvider $provider): void {
            if (empty($provider->id)) {
                $provider->id = Snowflake::id();
            }
        });
    }
}
