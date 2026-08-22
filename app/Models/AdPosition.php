<?php

/**
 * 广告位模型
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'pos_name',
    'pos_code',
    'pos_desc',
    'ad_width',
    'ad_height',
    'status',
])]
class AdPosition extends Model
{
    protected $table = 'ad_position';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (AdPosition $position): void {
            if (empty($position->id)) {
                $position->id = Snowflake::id();
            }
        });
    }

    public function materials(): HasMany
    {
        return $this->hasMany(AdMaterial::class, 'position_id');
    }
}
