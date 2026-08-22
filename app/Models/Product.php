<?php

/**
 * 商品模型
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
    'auto_code',
    'product_name',
    'product_model',
    'category_id',
    'brand_id',
    'material_quality',
    'filling',
    'short_desc',
    'main_image_url',
    'product_status',
    'sort_order',
    'created_by',
    'updated_by',
    'deleted_by',
])]
class Product extends Model
{
    use SoftDeletes;

    protected $table = 'product';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if (empty($product->id)) {
                $product->id = Snowflake::id();
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }

    public function skus(): HasMany
    {
        return $this->hasMany(ProductSku::class, 'product_id')->orderByDesc('sort_order')->orderBy('id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class, 'product_id')->orderByDesc('sort_order')->orderBy('id');
    }
}
