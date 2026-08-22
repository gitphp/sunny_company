<?php

namespace App\Models;

use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'position_id',
    'title',
    'image_url',
    'link_url',
    'target',
    'sort_order',
    'start_time',
    'end_time',
    'status',
])]
class AdMaterial extends Model
{
    protected $table = 'ad_material';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (AdMaterial $material): void {
            if (empty($material->id)) {
                $material->id = Snowflake::id();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(AdPosition::class, 'position_id');
    }

    public function scopeEffective(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('status', 1)
            ->where(function (Builder $builder) use ($now): void {
                $builder->whereNull('start_time')->orWhere('start_time', '<=', $now);
            })
            ->where(function (Builder $builder) use ($now): void {
                $builder->whereNull('end_time')->orWhere('end_time', '>=', $now);
            });
    }
}
