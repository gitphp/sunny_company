<?php

namespace App\Models;

use App\Enums\ArticleContentType;
use App\Enums\ArticleStatus;
use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title',
    'subtitle',
    'art_cover',
    'art_content',
    'content_type',
    'summary',
    'category_id',
    'tag_ids',
    'author_id',
    'author_name',
    'source',
    'source_url',
    'art_status',
    'is_top',
    'is_original',
    'is_commentable',
    'seo_title',
    'seo_keywords',
    'seo_description',
    'extra_fields',
    'published_at',
    'reviewer_id',
    'reviewed_at',
    'reject_reason',
])]
class Article extends Model
{
    use SoftDeletes;

    protected $table = 'articles';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (Article $article): void {
            if (empty($article->id)) {
                $article->id = Snowflake::id();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content_type' => ArticleContentType::class,
            'art_status' => ArticleStatus::class,
            'tag_ids' => 'array',
            'extra_fields' => 'array',
            'published_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
