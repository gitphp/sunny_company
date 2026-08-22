<?php

/**
 * 文章服务类
 *
 * @package     App\Services
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Services;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ArticleService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters): array
    {
        $paginator = $this->filteredQuery($filters)
            ->with('category:id,cat_name')
            ->orderByDesc('is_top')
            ->orderByDesc('created_at')
            ->paginate((int) ($filters['per_page'] ?? 10));

        return [
            'data' => collect($paginator->items())->map(fn (Article $article) => $this->transform($article))->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function find(string $id): array
    {
        $article = Article::query()->with('category:id,cat_name')->findOrFail($id);

        return [
            'article' => $this->transform($article, true),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data, User $operator): array
    {
        $article = DB::transaction(function () use ($data, $operator): Article {
            $article = new Article($this->payload($data, $operator, true));
            $this->applyStatusSideEffects($article, $operator);
            $article->save();

            return $article->load('category:id,cat_name');
        });

        return [
            'message' => '新增成功',
            'article' => $this->transform($article, true),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data, User $operator): array
    {
        $article = DB::transaction(function () use ($id, $data, $operator): Article {
            $article = Article::query()->findOrFail($id);
            $article->fill($this->payload($data, $operator, false));
            $this->applyStatusSideEffects($article, $operator);
            $article->save();

            return $article->fresh()->load('category:id,cat_name');
        });

        return [
            'message' => '修改成功',
            'article' => $this->transform($article, true),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id): array
    {
        Article::query()->findOrFail($id)->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @param  list<string>  $ids
     * @return array<string, string>
     */
    public function batchDelete(array $ids): array
    {
        Article::query()->whereIn('id', $ids)->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function changeStatus(string $id, array $data, User $operator): array
    {
        $article = Article::query()->findOrFail($id);

        if (array_key_exists('is_top', $data)) {
            $article->is_top = (int) $data['is_top'];
        }

        if (array_key_exists('art_status', $data)) {
            $status = (int) $data['art_status'];
            $article->art_status = $status;
            $article->reject_reason = (string) ($data['reject_reason'] ?? $article->reject_reason ?? '');
            $this->assertPublishable($status, [
                'title' => $article->title,
                'reject_reason' => $article->reject_reason,
            ], $article->category_id);
            $this->applyStatusSideEffects($article, $operator);
        }

        $article->save();

        return [
            'message' => '状态已更新',
            'article' => $this->transform($article->fresh()->load('category:id,cat_name')),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function filteredQuery(array $filters): Builder
    {
        return Article::query()
            ->select($this->listColumns())
            ->when(($filters['title'] ?? '') !== '', fn (Builder $query) => $query->where('title', 'like', '%'.$filters['title'].'%'))
            ->when(isset($filters['art_status']) && $filters['art_status'] !== '', fn (Builder $query) => $query->where('art_status', $filters['art_status']))
            ->when(isset($filters['is_top']) && $filters['is_top'] !== '', fn (Builder $query) => $query->where('is_top', $filters['is_top']))
            ->when(($filters['begin_time'] ?? '') !== '', fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['begin_time']))
            ->when(($filters['end_time'] ?? '') !== '', fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['end_time']))
            ->when(($filters['category_id'] ?? '') !== '' && ($filters['category_id'] ?? '0') !== '0', function (Builder $query) use ($filters): void {
                $query->whereIn('category_id', ArticleCategory::selfAndDescendantIds((string) $filters['category_id']));
            });
    }

    /**
     * @return list<string>
     */
    private function listColumns(): array
    {
        return [
            'id', 'title', 'subtitle', 'art_cover', 'summary', 'category_id', 'author_id', 'author_name',
            'source', 'art_status', 'is_top', 'is_original', 'view_count', 'published_at', 'created_at',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data, User $operator, bool $creating): array
    {
        $status = (int) ($data['art_status'] ?? ArticleStatus::Draft->value);
        $categoryId = ($data['category_id'] ?? 0) ?: 0;

        $this->assertPublishable($status, $data, $categoryId);

        $payload = [
            'title' => (string) ($data['title'] ?? ''),
            'subtitle' => (string) ($data['subtitle'] ?? ''),
            'art_cover' => (string) ($data['art_cover'] ?? ''),
            'art_content' => (string) ($data['art_content'] ?? ''),
            'content_type' => (int) ($data['content_type'] ?? 1),
            'summary' => (string) ($data['summary'] ?? ''),
            'category_id' => $categoryId,
            'tag_ids' => array_values(array_filter(array_map('strval', $data['tag_ids'] ?? []))),
            'source' => (string) ($data['source'] ?? ''),
            'source_url' => (string) ($data['source_url'] ?? ''),
            'art_status' => $status,
            'is_top' => (int) ($data['is_top'] ?? 0),
            'is_original' => (int) ($data['is_original'] ?? 1),
            'is_commentable' => (int) ($data['is_commentable'] ?? 1),
            'seo_title' => (string) ($data['seo_title'] ?? ''),
            'seo_keywords' => (string) ($data['seo_keywords'] ?? ''),
            'seo_description' => (string) ($data['seo_description'] ?? ''),
            'reject_reason' => (string) ($data['reject_reason'] ?? ''),
        ];

        if ($payload['source'] === '') {
            $payload['source'] = ((int) ($data['is_original'] ?? 1) === 1) ? '原创' : '转载';
        }

        if ($creating) {
            $payload['author_id'] = $operator->id;
            $payload['author_name'] = mb_substr((string) $operator->real_name, 0, 16);
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function assertPublishable(int $status, array $data, int|string $categoryId): void
    {
        if ($status !== ArticleStatus::Published->value) {
            if ($status === ArticleStatus::Rejected->value && trim((string) ($data['reject_reason'] ?? '')) === '') {
                throw ValidationException::withMessages([
                    'reject_reason' => ['驳回时请填写原因'],
                ]);
            }

            return;
        }

        if (trim((string) ($data['title'] ?? '')) === '') {
            throw ValidationException::withMessages([
                'title' => ['发布文章必须填写标题'],
            ]);
        }

        if ((string) $categoryId === '' || (string) $categoryId === '0') {
            throw ValidationException::withMessages([
                'category_id' => ['发布文章必须选择分类'],
            ]);
        }

        ArticleCategory::query()->findOrFail($categoryId);
    }

    private function applyStatusSideEffects(Article $article, User $operator): void
    {
        $status = $article->art_status instanceof ArticleStatus
            ? $article->art_status
            : ArticleStatus::from((int) $article->art_status);

        if ($status === ArticleStatus::Published && $article->published_at === null) {
            $article->published_at = now();
        }

        if (in_array($status, [ArticleStatus::Approved, ArticleStatus::Published, ArticleStatus::Rejected], true)) {
            $article->reviewer_id = $operator->id;
            $article->reviewed_at = now();
        }

        if ($status !== ArticleStatus::Rejected) {
            $article->reject_reason = (string) ($article->reject_reason ?: '');
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(Article $article, bool $detail = false): array
    {
        $data = [
            'id' => (string) $article->id,
            'title' => $article->title,
            'subtitle' => $article->subtitle,
            'art_cover' => $article->art_cover,
            'content_type' => $article->content_type?->value ?? $article->getAttribute('content_type'),
            'summary' => $article->summary,
            'category_id' => (string) $article->category_id,
            'category_name' => $article->category?->cat_name,
            'tag_ids' => collect($article->tag_ids ?? [])->map(fn ($id) => (string) $id)->all(),
            'author_id' => (string) $article->author_id,
            'author_name' => $article->author_name,
            'source' => $article->source,
            'source_url' => $article->source_url,
            'art_status' => $article->art_status?->value,
            'art_status_label' => $article->art_status?->label(),
            'is_top' => (int) $article->is_top,
            'is_original' => (int) $article->is_original,
            'is_commentable' => (int) $article->is_commentable,
            'view_count' => (int) $article->view_count,
            'published_at' => $article->published_at?->toDateTimeString(),
            'created_at' => $article->created_at?->toDateTimeString(),
            'reject_reason' => $article->reject_reason,
        ];

        if ($detail) {
            $data['art_content'] = $article->art_content;
            $data['seo_title'] = $article->seo_title;
            $data['seo_keywords'] = $article->seo_keywords;
            $data['seo_description'] = $article->seo_description;
            $data['extra_fields'] = $article->extra_fields ?? [];
            $data['reviewer_id'] = (string) $article->reviewer_id;
            $data['reviewed_at'] = $article->reviewed_at?->toDateTimeString();
        }

        return $data;
    }
}
