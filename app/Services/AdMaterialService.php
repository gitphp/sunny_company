<?php

namespace App\Services;

use App\Models\AdMaterial;
use App\Models\AdPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdMaterialService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters): array
    {
        $paginator = AdMaterial::query()
            ->with('position:id,pos_name,pos_code,ad_width,ad_height')
            ->when(($filters['title'] ?? '') !== '', fn (Builder $query) => $query->where('title', 'like', '%'.$filters['title'].'%'))
            ->when(($filters['position_id'] ?? '') !== '' && ($filters['position_id'] ?? '0') !== '0', fn (Builder $query) => $query->where('position_id', $filters['position_id']))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $query) => $query->where('status', $filters['status']))
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate((int) ($filters['per_page'] ?? 10));

        return [
            'data' => collect($paginator->items())->map(fn (AdMaterial $material) => $this->transform($material))->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        $material = DB::transaction(function () use ($data): AdMaterial {
            return AdMaterial::query()->create($this->payload($data));
        });

        return [
            'message' => '新增成功',
            'material' => $this->transform($material->load('position:id,pos_name,pos_code,ad_width,ad_height')),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data): array
    {
        $material = DB::transaction(function () use ($id, $data): AdMaterial {
            $material = AdMaterial::query()->findOrFail($id);
            $material->fill($this->payload($data))->save();

            return $material->fresh()->load('position:id,pos_name,pos_code,ad_width,ad_height');
        });

        return [
            'message' => '修改成功',
            'material' => $this->transform($material),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id): array
    {
        AdMaterial::query()->findOrFail($id)->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatus(string $id, int $status): array
    {
        $material = AdMaterial::query()->findOrFail($id);
        $material->forceFill(['status' => $status])->save();

        return [
            'message' => '状态已更新',
            'material' => $this->transform($material->fresh()->load('position:id,pos_name,pos_code')),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function activeByCode(string $code): array
    {
        $position = AdPosition::query()
            ->where('pos_code', $code)
            ->where('status', 1)
            ->first();

        if (! $position) {
            return [
                'position' => null,
                'materials' => [],
            ];
        }

        $materials = $position->materials()
            ->effective()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return [
            'position' => [
                'id' => (string) $position->id,
                'pos_name' => $position->pos_name,
                'pos_code' => $position->pos_code,
                'ad_width' => (int) $position->ad_width,
                'ad_height' => (int) $position->ad_height,
            ],
            'materials' => $materials->map(fn (AdMaterial $material) => [
                'id' => (string) $material->id,
                'title' => $material->title,
                'image_url' => $material->image_url,
                'link_url' => $material->link_url,
                'target' => $material->target,
                'sort_order' => $material->sort_order,
            ])->values(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        $positionId = (string) ($data['position_id'] ?? '0');

        if ($positionId === '' || $positionId === '0') {
            throw ValidationException::withMessages([
                'position_id' => ['请选择广告位'],
            ]);
        }

        AdPosition::query()->findOrFail($positionId);

        $start = $data['start_time'] ?? null;
        $end = $data['end_time'] ?? null;

        if ($start && $end && $end < $start) {
            throw ValidationException::withMessages([
                'end_time' => ['结束时间不能早于开始时间'],
            ]);
        }

        return [
            'position_id' => $positionId,
            'title' => (string) ($data['title'] ?? ''),
            'image_url' => (string) ($data['image_url'] ?? ''),
            'link_url' => (string) ($data['link_url'] ?? ''),
            'target' => (string) ($data['target'] ?? '_blank'),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'start_time' => $start ?: null,
            'end_time' => $end ?: null,
            'status' => (int) ($data['status'] ?? 1),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(AdMaterial $material): array
    {
        return [
            'id' => (string) $material->id,
            'position_id' => (string) $material->position_id,
            'position_name' => $material->position?->pos_name,
            'position_code' => $material->position?->pos_code,
            'title' => $material->title,
            'image_url' => $material->image_url,
            'link_url' => $material->link_url,
            'target' => $material->target,
            'sort_order' => $material->sort_order,
            'start_time' => $material->start_time?->toDateTimeString(),
            'end_time' => $material->end_time?->toDateTimeString(),
            'status' => (int) $material->status,
            'created_at' => $material->created_at?->toDateTimeString(),
        ];
    }
}
