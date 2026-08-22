<?php

namespace App\Models;

use App\Enums\JobStatus;
use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'job_title',
    'department',
    'workplace',
    'experience',
    'education',
    'salary_range',
    'description',
    'requirements',
    'benefits',
    'is_hot',
    'job_status',
    'expire_at',
    'job_sort',
])]
class BossJob extends Model
{
    use SoftDeletes;

    protected $table = 'boss_job';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (BossJob $job): void {
            if (empty($job->id)) {
                $job->id = Snowflake::id();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'job_status' => JobStatus::class,
            'expire_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('job_status', JobStatus::Published)
            ->where(function (Builder $builder): void {
                $builder->whereNull('expire_at')->orWhere('expire_at', '>=', now());
            });
    }
}
