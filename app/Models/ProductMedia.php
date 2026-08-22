<?php

/**
 * 商品媒体模型
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
    'product_id',
    'media_type',
    'file_url',
    'file_name',
    'file_key',
    'storage_provider',
    'extension',
    'file_size',
    'file_type',
    'sort_order',
    'created_by',
    'updated_by',
    'deleted_by',
])]
class ProductMedia extends Model
{
    use SoftDeletes;

    protected $table = 'product_media';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (ProductMedia $media): void {
            if (empty($media->id)) {
                $media->id = Snowflake::id();
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
