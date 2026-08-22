<?php

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
