<?php

namespace App\Services;

use App\Models\AdPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdPositionService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters): array
    {
        $paginator = AdPosition::query()
            ->withCount('materials')
            ->when(($filters['pos_name'] ?? '') !== '', fn (Builder $query) => $query->where('pos_name', 'like', '%'.$filters['pos_name'].'%'))
            ->when(($filters['pos_code'] ?? '') !== '', fn (Builder $query) => $query->where('pos_code', 'like', '%'.$filters['pos_code'].'%'))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $query) => $query->where('status', $filters['status']))
            ->orderByDesc('id')
            ->paginate((int) ($filters['per_page'] ?? 10));

        return [
            'data' => collect($paginator->items())->map(fn (AdPosition $position) => $this->transform($position))->values(),
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
        $position = DB::transaction(fn (): AdPosition => AdPosition::query()->create($this->payload($data)));

        return [
            'message' => '新增成功',
            'position' => $this->transform($position),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data): array
    {
        $position = DB::transaction(function () use ($id, $data): AdPosition {
            $position = AdPosition::query()->findOrFail($id);
            $position->fill($this->payload($data))->save();

            return $position->fresh();
        });

        return [
            'message' => '修改成功',
            'position' => $this->transform($position),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id): array
    {
        $position = AdPosition::query()->findOrFail($id);

        if ($position->materials()->exists()) {
            throw ValidationException::withMessages([
                'id' => ['该广告位下仍有素材，无法删除'],
            ]);
        }

        $position->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatus(string $id, int $status): array
    {
        $position = AdPosition::query()->findOrFail($id);
        $position->forceFill(['status' => $status])->save();

        return [
            'message' => '状态已更新',
            'position' => $this->transform($position->fresh()),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        return [
            'pos_name' => (string) ($data['pos_name'] ?? ''),
            'pos_code' => (string) ($data['pos_code'] ?? ''),
            'pos_desc' => (string) ($data['pos_desc'] ?? ''),
            'ad_width' => (int) ($data['ad_width'] ?? 0),
            'ad_height' => (int) ($data['ad_height'] ?? 0),
            'status' => (int) ($data['status'] ?? 1),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(AdPosition $position): array
    {
        return [
            'id' => (string) $position->id,
            'pos_name' => $position->pos_name,
            'pos_code' => $position->pos_code,
            'pos_desc' => $position->pos_desc,
            'ad_width' => (int) $position->ad_width,
            'ad_height' => (int) $position->ad_height,
            'status' => (int) $position->status,
            'material_count' => (int) ($position->materials_count ?? $position->materials()->count()),
            'created_at' => $position->created_at?->toDateTimeString(),
        ];
    }
}
