<?php

/**
 * 友情链接服务类
 *
 * @package     App\Services
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Services;

use App\Models\FriendLink;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FriendLinkService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters): array
    {
        $paginator = FriendLink::query()
            ->when(($filters['link_name'] ?? '') !== '', fn (Builder $query) => $query->where('link_name', 'like', '%'.$filters['link_name'].'%'))
            ->when(isset($filters['link_status']) && $filters['link_status'] !== '', fn (Builder $query) => $query->where('link_status', $filters['link_status']))
            ->orderBy('link_sort')
            ->orderByDesc('id')
            ->paginate((int) ($filters['per_page'] ?? 10));

        return [
            'data' => collect($paginator->items())->map(fn (FriendLink $link) => $this->transform($link))->values(),
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
        $link = DB::transaction(fn (): FriendLink => FriendLink::query()->create($this->payload($data)));

        return [
            'message' => '新增成功',
            'link' => $this->transform($link),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data): array
    {
        $link = DB::transaction(function () use ($id, $data): FriendLink {
            $link = FriendLink::query()->findOrFail($id);
            $link->fill($this->payload($data))->save();

            return $link->fresh();
        });

        return [
            'message' => '修改成功',
            'link' => $this->transform($link),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id): array
    {
        FriendLink::query()->findOrFail($id)->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatus(string $id, int $status): array
    {
        $link = FriendLink::query()->findOrFail($id);
        $link->forceFill(['link_status' => $status])->save();

        return [
            'message' => '状态已更新',
            'link' => $this->transform($link->fresh()),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        return [
            'link_name' => (string) ($data['link_name'] ?? ''),
            'link_url' => (string) ($data['link_url'] ?? ''),
            'link_logo' => (string) ($data['link_logo'] ?? ''),
            'link_desc' => (string) ($data['link_desc'] ?? ''),
            'link_sort' => (int) ($data['link_sort'] ?? 0),
            'link_status' => (int) ($data['link_status'] ?? 1),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(FriendLink $link): array
    {
        return [
            'id' => (string) $link->id,
            'link_name' => $link->link_name,
            'link_url' => $link->link_url,
            'link_logo' => $link->link_logo,
            'link_desc' => $link->link_desc,
            'link_sort' => $link->link_sort,
            'link_status' => (int) $link->link_status,
            'created_at' => $link->created_at?->toDateTimeString(),
        ];
    }
}
