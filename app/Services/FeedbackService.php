<?php

/**
 * 留言服务类
 *
 * @package     App\Services
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Services;

use App\Enums\FeedbackStatus;
use App\Exceptions\BusinessException;
use App\Models\Feedback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FeedbackService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters): array
    {
        $paginator = Feedback::query()
            ->when(($filters['keyword'] ?? '') !== '', function (Builder $query) use ($filters): void {
                $keyword = '%'.$filters['keyword'].'%';
                $query->where(function (Builder $builder) use ($keyword): void {
                    $builder->where('fb_name', 'like', $keyword)
                        ->orWhere('fb_title', 'like', $keyword)
                        ->orWhere('fb_phone', 'like', $keyword)
                        ->orWhere('fb_email', 'like', $keyword);
                });
            })
            ->when(isset($filters['fb_status']) && $filters['fb_status'] !== '', fn (Builder $query) => $query->where('fb_status', $filters['fb_status']))
            ->when(($filters['begin_time'] ?? '') !== '', fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['begin_time']))
            ->when(($filters['end_time'] ?? '') !== '', fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['end_time']))
            ->orderByDesc('created_at')
            ->paginate((int) ($filters['per_page'] ?? 10));

        return [
            'data' => collect($paginator->items())->map(fn (Feedback $feedback) => $this->transform($feedback))->values(),
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
        return [
            'feedback' => $this->transform(Feedback::query()->findOrFail($id), true),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data, string $ip): array
    {
        $feedback = Feedback::query()->create([
            'fb_name' => (string) ($data['fb_name'] ?? ''),
            'fb_phone' => (string) ($data['fb_phone'] ?? ''),
            'fb_email' => (string) ($data['fb_email'] ?? ''),
            'fb_company' => (string) ($data['fb_company'] ?? ''),
            'fb_title' => (string) ($data['fb_title'] ?? ''),
            'fb_content' => (string) ($data['fb_content'] ?? ''),
            'fb_status' => FeedbackStatus::Pending,
            'ip' => mb_substr($ip, 0, 32),
        ]);

        return [
            'message' => '提交成功',
            'feedback' => $this->transform($feedback),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function reply(string $id, array $data): array
    {
        $reply = trim((string) ($data['reply_content'] ?? ''));

        if ($reply === '') {
            BusinessException::fail('请填写回复内容', 'reply_content');
        }

        $feedback = DB::transaction(function () use ($id, $reply): Feedback {
            $feedback = Feedback::query()->findOrFail($id);
            $feedback->forceFill([
                'reply_content' => $reply,
                'replied_at' => now(),
                'fb_status' => FeedbackStatus::Processed,
            ])->save();

            return $feedback->fresh();
        });

        return [
            'message' => '回复成功',
            'feedback' => $this->transform($feedback, true),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatus(string $id, int $status): array
    {
        $feedback = Feedback::query()->findOrFail($id);
        $feedback->forceFill(['fb_status' => $status])->save();

        return [
            'message' => '状态已更新',
            'feedback' => $this->transform($feedback->fresh()),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id): array
    {
        Feedback::query()->findOrFail($id)->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @param  list<string>  $ids
     * @return array<string, string>
     */
    public function batchDelete(array $ids): array
    {
        Feedback::query()->whereIn('id', $ids)->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(Feedback $feedback, bool $detail = false): array
    {
        $data = [
            'id' => (string) $feedback->id,
            'fb_name' => $feedback->fb_name,
            'fb_phone' => $feedback->fb_phone,
            'fb_email' => $feedback->fb_email,
            'fb_company' => $feedback->fb_company,
            'fb_title' => $feedback->fb_title,
            'fb_status' => $feedback->fb_status?->value,
            'fb_status_label' => $feedback->fb_status?->label(),
            'replied_at' => $feedback->replied_at?->toDateTimeString(),
            'ip' => $feedback->ip,
            'created_at' => $feedback->created_at?->toDateTimeString(),
        ];

        if ($detail) {
            $data['fb_content'] = $feedback->fb_content;
            $data['reply_content'] = $feedback->reply_content;
        }

        return $data;
    }
}
