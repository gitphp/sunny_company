<?php

namespace App\Models;

use App\Enums\ConfigGroup;
use App\Enums\ConfigInputType;
use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'conf_group',
    'conf_key',
    'conf_value',
    'conf_desc',
    'input_type',
    'conf_sort',
])]
class SiteConfig extends Model
{
    protected $table = 'site_configs';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (SiteConfig $config): void {
            if (empty($config->id)) {
                $config->id = Snowflake::id();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'conf_group' => ConfigGroup::class,
            'input_type' => ConfigInputType::class,
        ];
    }
}
