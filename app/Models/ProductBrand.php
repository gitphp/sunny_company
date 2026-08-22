<?php

namespace App\Models;

use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'brand_code',
    'brand_name',
    'alias',
    'is_system',
    'is_show',
    'sort_order',
    'brand_remark',
    'created_by',
    'updated_by',
    'deleted_by',
])]
class ProductBrand extends Model
{
    use SoftDeletes;

    protected $table = 'product_brand';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (ProductBrand $brand): void {
            if (empty($brand->id)) {
                $brand->id = Snowflake::id();
            }
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
