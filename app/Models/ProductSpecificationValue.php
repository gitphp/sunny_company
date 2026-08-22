<?php

/**
 * 商品规格值模型
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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'spec_id',
    'value_code',
    'value',
    'sort_order',
    'value_status',
    'created_by',
    'updated_by',
    'deleted_by',
])]
class ProductSpecificationValue extends Model
{
    use SoftDeletes;

    protected $table = 'product_specification_value';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (ProductSpecificationValue $value): void {
            if (empty($value->id)) {
                $value->id = Snowflake::id();
            }
        });
    }

    public function spec(): BelongsTo
    {
        return $this->belongsTo(ProductSpecification::class, 'spec_id');
    }

    public function skuLinks(): HasMany
    {
        return $this->hasMany(ProductSkuSpecValue::class, 'spec_value_id');
    }
}
