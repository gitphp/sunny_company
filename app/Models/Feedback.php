<?php

namespace App\Models;

use App\Enums\FeedbackStatus;
use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'fb_name',
    'fb_phone',
    'fb_email',
    'fb_company',
    'fb_title',
    'fb_content',
    'fb_status',
    'reply_content',
    'replied_at',
    'ip',
])]
class Feedback extends Model
{
    protected $table = 'feedbacks';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (Feedback $feedback): void {
            if (empty($feedback->id)) {
                $feedback->id = Snowflake::id();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fb_status' => FeedbackStatus::class,
            'replied_at' => 'datetime',
        ];
    }
}
