<?php

/**
 * 岗位服务类
 *
 * @package     App\Services
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\HrPost;
use Illuminate\Support\Facades\DB;

class PostService
{
    /**
     * @return array<string, mixed>
     */
    public function tree(): array
    {
        return [
            'posts' => HrPost::buildTree(HrPost::ordered()),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data, string $operatorId): array
    {
        $post = DB::transaction(function () use ($data, $operatorId): HrPost {
            $post = new HrPost($this->payload($data));
            $post->parent_id = $this->parentId($data);
            $post->created_by = $operatorId ?: 0;
            $post->save();

            return $post;
        });

        return [
            'message' => '新增成功',
            'post' => $this->transform($post),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data): array
    {
        $post = DB::transaction(function () use ($id, $data): HrPost {
            $post = HrPost::query()->findOrFail($id);
            $parentId = $this->parentId($data);

            if ($this->isSelfOrDescendant($post, $parentId)) {
                BusinessException::fail('不能选择自己或下级作为父岗位', 'parent_id');
            }

            $post->fill($this->payload($data));
            $post->parent_id = $parentId;
            $post->save();

            return $post->fresh();
        });

        return [
            'message' => '修改成功',
            'post' => $this->transform($post),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id): array
    {
        $post = HrPost::query()->findOrFail($id);

        if ($post->children()->exists()) {
            BusinessException::fail('请先删除下级岗位', 'id');
        }

        if ($post->users()->exists()) {
            BusinessException::fail('该岗位仍有用户，无法删除', 'id');
        }

        $post->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        return [
            'post_name' => (string) ($data['post_name'] ?? ''),
            'post_code' => (string) ($data['post_code'] ?? ''),
            'post_sort' => (int) ($data['post_sort'] ?? 0),
            'post_status' => (int) ($data['post_status'] ?? 1),
            'remark' => (string) ($data['remark'] ?? ''),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function parentId(array $data): int|string
    {
        $parentId = (string) ($data['parent_id'] ?? '0');

        if ($parentId === '' || $parentId === '0') {
            return 0;
        }

        HrPost::query()->findOrFail($parentId);

        return $parentId;
    }

    private function isSelfOrDescendant(HrPost $post, int|string $parentId): bool
    {
        $parentId = (string) $parentId;

        if ($parentId === '' || $parentId === '0') {
            return false;
        }

        return $parentId === (string) $post->id
            || in_array($parentId, HrPost::descendantIds((string) $post->id), true);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(HrPost $post): array
    {
        return [
            'id' => (string) $post->id,
            'parent_id' => (string) $post->parent_id,
            'post_name' => $post->post_name,
            'post_code' => $post->post_code,
            'post_sort' => $post->post_sort,
            'post_status' => $post->post_status,
            'remark' => $post->remark,
        ];
    }
}
