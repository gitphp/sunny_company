<?php

namespace App\Models;

use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'product_id',
    'sku_code',
    'price',
    'market_price',
    'cost_price',
    'stock_num',
    'weight',
    'volume',
    'sale_status',
    'sort_order',
    'created_by',
    'updated_by',
    'deleted_by',
])]
class ProductSku extends Model
{
    use SoftDeletes;

    protected $table = 'product_sku';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'market_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'volume' => 'decimal:4',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ProductSku $sku): void {
            if (empty($sku->id)) {
                $sku->id = Snowflake::id();
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function specValues(): HasMany
    {
        return $this->hasMany(ProductSkuSpecValue::class, 'sku_id');
    }
}
