<?php

namespace App\Models;

use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'spec_code',
    'spec_name',
    'spec_remark',
    'spec_status',
    'sort_order',
    'created_by',
    'updated_by',
    'deleted_by',
])]
class ProductSpecification extends Model
{
    use SoftDeletes;

    protected $table = 'product_specification';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (ProductSpecification $spec): void {
            if (empty($spec->id)) {
                $spec->id = Snowflake::id();
            }
        });
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductSpecificationValue::class, 'spec_id')->orderByDesc('sort_order')->orderBy('id');
    }
}
