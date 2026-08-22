<?php

namespace App\Models;

use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'operator_id',
    'operator_name',
    'biz_type',
    'activity_type',
    'action',
    'biz_id',
    'biz_label',
    'old_value',
    'new_value',
    'operator_status',
    'error_msg',
    'client_ip',
    'user_agent',
    'request_url',
    'method_fun',
    'created_at',
])]
class OperationLog extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'operation_log';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (OperationLog $log): void {
            if (empty($log->id)) {
                $log->id = Snowflake::id();
            }

            if ($log->created_at === null) {
                $log->created_at = now();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'old_value' => 'array',
            'new_value' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
