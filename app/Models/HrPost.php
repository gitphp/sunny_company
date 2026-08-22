<?php

namespace App\Models;

use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

#[Fillable([
    'parent_id',
    'post_name',
    'post_code',
    'post_sort',
    'post_status',
    'remark',
    'created_by',
])]
class HrPost extends Model
{
    use SoftDeletes;

    protected $table = 'hr_post';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (HrPost $post): void {
            if (empty($post->id)) {
                $post->id = Snowflake::id();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderByDesc('post_sort');
    }

    /**
     * @return Collection<int, HrPost>
     */
    public static function ordered(): Collection
    {
        return static::query()
            ->orderByDesc('post_sort')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return list<string>
     */
    public static function descendantIds(string $postId): array
    {
        $ids = [];
        $pending = [$postId];

        while ($pending !== []) {
            $children = static::query()
                ->whereIn('parent_id', $pending)
                ->pluck('id')
                ->map(fn ($id): string => (string) $id)
                ->all();

            $pending = [];

            foreach ($children as $childId) {
                if ($childId === $postId || in_array($childId, $ids, true)) {
                    continue;
                }

                $ids[] = $childId;
                $pending[] = $childId;
            }
        }

        return $ids;
    }

    /**
     * @param  Collection<int, HrPost>  $posts
     * @return array<int, array<string, mixed>>
     */
    public static function buildTree(Collection $posts, string $parentId = '0'): array
    {
        $branch = [];

        foreach ($posts as $post) {
            if ((string) $post->parent_id !== $parentId) {
                continue;
            }

            $item = [
                'id' => (string) $post->id,
                'parent_id' => (string) $post->parent_id,
                'post_name' => $post->post_name,
                'post_code' => $post->post_code,
                'post_sort' => $post->post_sort,
                'post_status' => $post->post_status,
                'remark' => $post->remark,
            ];

            $children = self::buildTree($posts, (string) $post->id);

            if ($children !== []) {
                $item['children'] = $children;
            }

            $branch[] = $item;
        }

        return $branch;
    }
}
