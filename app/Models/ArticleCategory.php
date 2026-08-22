<?php

namespace App\Models;

use App\Enums\CategoryType;
use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

#[Fillable([
    'cat_type',
    'parent_id',
    'cat_name',
    'cat_url',
    'description',
    'cat_sort',
    'status',
])]
class ArticleCategory extends Model
{
    use SoftDeletes;

    protected $table = 'article_category';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (ArticleCategory $category): void {
            if (empty($category->id)) {
                $category->id = Snowflake::id();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cat_type' => CategoryType::class,
            'deleted_at' => 'datetime',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderByDesc('cat_sort');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'category_id');
    }

    /**
     * @return Collection<int, ArticleCategory>
     */
    public static function ordered(?int $type = null): Collection
    {
        return static::query()
            ->when($type !== null, fn ($query) => $query->where('cat_type', $type))
            ->orderByDesc('cat_sort')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return list<string>
     */
    public static function selfAndDescendantIds(string $categoryId): array
    {
        $ids = [$categoryId];
        $pending = [$categoryId];

        while ($pending !== []) {
            $children = static::query()
                ->whereIn('parent_id', $pending)
                ->pluck('id')
                ->map(fn ($id): string => (string) $id)
                ->all();

            $pending = [];

            foreach ($children as $childId) {
                if (in_array($childId, $ids, true)) {
                    continue;
                }

                $ids[] = $childId;
                $pending[] = $childId;
            }
        }

        return $ids;
    }

    /**
     * @param  Collection<int, ArticleCategory>  $categories
     * @return array<int, array<string, mixed>>
     */
    public static function buildTree(Collection $categories, string $parentId = '0'): array
    {
        $branch = [];

        foreach ($categories as $category) {
            if ((string) $category->parent_id !== $parentId) {
                continue;
            }

            $item = [
                'id' => (string) $category->id,
                'parent_id' => (string) $category->parent_id,
                'cat_type' => $category->cat_type?->value,
                'cat_type_label' => $category->cat_type?->label(),
                'cat_name' => $category->cat_name,
                'cat_url' => $category->cat_url,
                'description' => $category->description,
                'cat_sort' => $category->cat_sort,
                'status' => $category->status,
            ];

            $children = self::buildTree($categories, (string) $category->id);

            if ($children !== []) {
                $item['children'] = $children;
            }

            $branch[] = $item;
        }

        return $branch;
    }
}
