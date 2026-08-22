<?php

/**
 * 商品SKU规格关联模型
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
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'sku_id',
    'spec_id',
    'spec_value_id',
    'created_by',
    'updated_by',
    'deleted_by',
])]
class ProductSkuSpecValue extends Model
{
    use SoftDeletes;

    protected $table = 'product_sku_spec_value';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (ProductSkuSpecValue $link): void {
            if (empty($link->id)) {
                $link->id = Snowflake::id();
            }
        });
    }

    public function sku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class, 'sku_id');
    }

    public function spec(): BelongsTo
    {
        return $this->belongsTo(ProductSpecification::class, 'spec_id');
    }

    public function specValue(): BelongsTo
    {
        return $this->belongsTo(ProductSpecificationValue::class, 'spec_value_id');
    }
}
