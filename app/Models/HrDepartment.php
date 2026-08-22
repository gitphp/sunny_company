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
    'dept_name',
    'dept_code',
    'ancestors',
    'dept_level',
    'leader_user_id',
    'dept_phone',
    'dept_sort',
    'dept_status',
    'created_by',
])]
class HrDepartment extends Model
{
    use SoftDeletes;

    protected $table = 'hr_department';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (HrDepartment $department): void {
            if (empty($department->id)) {
                $department->id = Snowflake::id();
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
        return $this->hasMany(self::class, 'parent_id')->orderByDesc('dept_sort');
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_user_id');
    }

    /**
     * @return list<string>
     */
    public static function selfAndDescendantIds(string $deptId): array
    {
        return static::query()
            ->where('id', $deptId)
            ->orWhereRaw('FIND_IN_SET(?, ancestors)', [$deptId])
            ->pluck('id')
            ->map(fn ($id): string => (string) $id)
            ->all();
    }

    /**
     * @return Collection<int, HrDepartment>
     */
    public static function ordered(): Collection
    {
        return static::query()
            ->orderByDesc('dept_sort')
            ->orderBy('id')
            ->get();
    }

    /**
     * @param  Collection<int, HrDepartment>  $departments
     * @return array<int, array<string, mixed>>
     */
    public static function buildTree(Collection $departments, string $parentId = '0'): array
    {
        $branch = [];

        foreach ($departments as $department) {
            if ((string) $department->parent_id !== $parentId) {
                continue;
            }

            $item = [
                'id' => (string) $department->id,
                'parent_id' => (string) $department->parent_id,
                'dept_name' => $department->dept_name,
                'dept_code' => $department->dept_code,
                'ancestors' => $department->ancestors,
                'dept_level' => $department->dept_level,
                'leader_user_id' => (string) $department->leader_user_id,
                'dept_phone' => $department->dept_phone,
                'dept_sort' => $department->dept_sort,
                'dept_status' => $department->dept_status,
            ];

            $children = self::buildTree($departments, (string) $department->id);

            if ($children !== []) {
                $item['children'] = $children;
            }

            $branch[] = $item;
        }

        return $branch;
    }

    public function fillHierarchy(?self $parent): void
    {
        if ($parent === null) {
            $this->parent_id = 0;
            $this->ancestors = '0';
            $this->dept_level = 1;

            return;
        }

        $this->parent_id = $parent->id;
        $this->ancestors = trim($parent->ancestors.','.$parent->id, ',');
        $this->dept_level = (int) $parent->dept_level + 1;
    }
}
