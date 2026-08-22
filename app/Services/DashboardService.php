<?php

namespace App\Services;

use App\Enums\ArticleStatus;
use App\Enums\FeedbackStatus;
use App\Enums\JobStatus;
use App\Enums\UserStatus;
use App\Models\AdMaterial;
use App\Models\AdPosition;
use App\Models\Article;
use App\Models\BossJob;
use App\Models\Feedback;
use App\Models\FriendLink;
use App\Models\OperationLog;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function overview(): array
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        return [
            'cards' => [
                [
                    'key' => 'users',
                    'label' => '用户总数',
                    'value' => User::query()->count(),
                    'today' => User::query()->whereDate('created_at', $today)->count(),
                    'yesterday' => User::query()->whereDate('created_at', $yesterday)->count(),
                    'extra' => User::query()->where('user_status', UserStatus::Normal)->count(),
                    'extra_label' => '正常账号',
                    'path' => '/system/user',
                ],
                [
                    'key' => 'articles',
                    'label' => '文章总数',
                    'value' => Article::query()->count(),
                    'today' => Article::query()->whereDate('created_at', $today)->count(),
                    'yesterday' => Article::query()->whereDate('created_at', $yesterday)->count(),
                    'extra' => Article::query()->where('art_status', ArticleStatus::Published)->count(),
                    'extra_label' => '已发布',
                    'path' => '/site/article',
                ],
                [
                    'key' => 'jobs',
                    'label' => '招聘职位',
                    'value' => BossJob::query()->count(),
                    'today' => BossJob::query()->whereDate('created_at', $today)->count(),
                    'yesterday' => BossJob::query()->whereDate('created_at', $yesterday)->count(),
                    'extra' => BossJob::query()->where('job_status', JobStatus::Published)->count(),
                    'extra_label' => '发布中',
                    'path' => '/site/job',
                ],
                [
                    'key' => 'feedbacks',
                    'label' => '留言反馈',
                    'value' => Feedback::query()->count(),
                    'today' => Feedback::query()->whereDate('created_at', $today)->count(),
                    'yesterday' => Feedback::query()->whereDate('created_at', $yesterday)->count(),
                    'extra' => Feedback::query()->where('fb_status', FeedbackStatus::Pending)->count(),
                    'extra_label' => '待处理',
                    'path' => '/site/feedback',
                ],
                [
                    'key' => 'logs',
                    'label' => '今日操作',
                    'value' => OperationLog::query()->whereDate('created_at', $today)->count(),
                    'today' => OperationLog::query()->whereDate('created_at', $today)->count(),
                    'yesterday' => OperationLog::query()->whereDate('created_at', $yesterday)->count(),
                    'extra' => OperationLog::query()->whereDate('created_at', $today)->where('operator_status', 0)->count(),
                    'extra_label' => '失败',
                    'path' => '/system/log/operlog',
                ],
                [
                    'key' => 'ads',
                    'label' => '广告素材',
                    'value' => AdMaterial::query()->count(),
                    'today' => AdMaterial::query()->whereDate('created_at', $today)->count(),
                    'yesterday' => AdMaterial::query()->whereDate('created_at', $yesterday)->count(),
                    'extra' => AdMaterial::query()->where('status', 1)->count(),
                    'extra_label' => '上线中',
                    'path' => '/site/ad-material',
                ],
                [
                    'key' => 'links',
                    'label' => '友情链接',
                    'value' => FriendLink::query()->count(),
                    'today' => FriendLink::query()->whereDate('created_at', $today)->count(),
                    'yesterday' => FriendLink::query()->whereDate('created_at', $yesterday)->count(),
                    'extra' => FriendLink::query()->where('link_status', 1)->count(),
                    'extra_label' => '启用',
                    'path' => '/site/link',
                ],
                [
                    'key' => 'views',
                    'label' => '内容浏览',
                    'value' => (int) Article::query()->sum('view_count') + (int) BossJob::query()->sum('view_count'),
                    'today' => 0,
                    'yesterday' => 0,
                    'extra' => AdPosition::query()->where('status', 1)->count(),
                    'extra_label' => '启用广告位',
                    'path' => '/site/article',
                ],
            ],
            'todos' => [
                [
                    'label' => '待审核文章',
                    'value' => Article::query()->where('art_status', ArticleStatus::Pending)->count(),
                    'path' => '/site/article',
                ],
                [
                    'label' => '未处理留言',
                    'value' => Feedback::query()->where('fb_status', FeedbackStatus::Pending)->count(),
                    'path' => '/site/feedback',
                ],
                [
                    'label' => '待发布职位',
                    'value' => BossJob::query()->where('job_status', JobStatus::Pending)->count(),
                    'path' => '/site/job',
                ],
                [
                    'label' => '急聘职位',
                    'value' => BossJob::query()->where('job_status', JobStatus::Published)->where('is_hot', 1)->count(),
                    'path' => '/site/job',
                ],
            ],
            'trends' => [
                'labels' => $this->trendDays()->pluck('label')->values(),
                'series' => [
                    ['name' => '新增用户', 'data' => $this->dailyCounts(User::class)],
                    ['name' => '新增文章', 'data' => $this->dailyCounts(Article::class)],
                    ['name' => '新增留言', 'data' => $this->dailyCounts(Feedback::class)],
                    ['name' => '操作日志', 'data' => $this->dailyCounts(OperationLog::class)],
                ],
            ],
            'article_status' => $this->articleStatus(),
            'job_status' => $this->jobStatus(),
            'recent_feedbacks' => Feedback::query()
                ->orderByDesc('id')
                ->limit(6)
                ->get()
                ->map(fn (Feedback $item) => [
                    'id' => (string) $item->id,
                    'fb_title' => $item->fb_title,
                    'fb_name' => $item->fb_name,
                    'fb_status' => $item->fb_status?->value,
                    'fb_status_label' => $item->fb_status?->label(),
                    'created_at' => $item->created_at?->toDateTimeString(),
                ])->values(),
            'recent_logs' => OperationLog::query()
                ->orderByDesc('id')
                ->limit(8)
                ->get()
                ->map(fn (OperationLog $log) => [
                    'id' => (string) $log->id,
                    'operator_name' => $log->operator_name,
                    'biz_type' => $log->biz_type,
                    'action' => $log->action,
                    'biz_label' => $log->biz_label,
                    'operator_status' => (int) $log->operator_status,
                    'created_at' => $log->created_at?->toDateTimeString(),
                ])->values(),
            'hot_jobs' => BossJob::query()
                ->where('job_status', JobStatus::Published)
                ->orderByDesc('view_count')
                ->orderByDesc('is_hot')
                ->limit(5)
                ->get()
                ->map(fn (BossJob $job) => [
                    'id' => (string) $job->id,
                    'job_title' => $job->job_title,
                    'department' => $job->department,
                    'workplace' => $job->workplace,
                    'is_hot' => (int) $job->is_hot,
                    'view_count' => (int) $job->view_count,
                ])->values(),
        ];
    }

    /**
     * @return list<int>
     */
    private function dailyCounts(string $model): array
    {
        $start = now()->subDays(6)->startOfDay();
        $rows = $model::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'day');

        return $this->trendDays()
            ->map(fn (array $day) => (int) ($rows[$day['date']] ?? 0))
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, array{date: string, label: string}>
     */
    private function trendDays(): Collection
    {
        return collect(range(6, 0))->map(fn (int $offset) => [
            'date' => now()->subDays($offset)->toDateString(),
            'label' => now()->subDays($offset)->format('m-d'),
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function articleStatus(): array
    {
        $counts = Article::query()
            ->selectRaw('art_status, COUNT(*) as total')
            ->groupBy('art_status')
            ->pluck('total', 'art_status');

        return array_map(fn (ArticleStatus $status) => [
            'value' => $status->value,
            'label' => $status->label(),
            'count' => (int) ($counts[$status->value] ?? 0),
        ], ArticleStatus::cases());
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function jobStatus(): array
    {
        $counts = BossJob::query()
            ->selectRaw('job_status, COUNT(*) as total')
            ->groupBy('job_status')
            ->pluck('total', 'job_status');

        return array_map(fn (JobStatus $status) => [
            'value' => $status->value,
            'label' => $status->label(),
            'count' => (int) ($counts[$status->value] ?? 0),
        ], JobStatus::cases());
    }
}
