<?php

namespace App\Services;

use App\Enums\JobStatus;
use App\Models\BossJob;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BossJobService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters): array
    {
        $paginator = BossJob::query()
            ->when(($filters['job_title'] ?? '') !== '', fn (Builder $query) => $query->where('job_title', 'like', '%'.$filters['job_title'].'%'))
            ->when(($filters['department'] ?? '') !== '', fn (Builder $query) => $query->where('department', 'like', '%'.$filters['department'].'%'))
            ->when(isset($filters['job_status']) && $filters['job_status'] !== '', fn (Builder $query) => $query->where('job_status', $filters['job_status']))
            ->when(isset($filters['is_hot']) && $filters['is_hot'] !== '', fn (Builder $query) => $query->where('is_hot', $filters['is_hot']))
            ->orderByDesc('is_hot')
            ->orderByDesc('job_sort')
            ->orderByDesc('id')
            ->paginate((int) ($filters['per_page'] ?? 10));

        return [
            'data' => collect($paginator->items())->map(fn (BossJob $job) => $this->transform($job))->values(),
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
            'job' => $this->transform(BossJob::query()->findOrFail($id), true),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        $job = DB::transaction(fn (): BossJob => BossJob::query()->create($this->payload($data)));

        return [
            'message' => '新增成功',
            'job' => $this->transform($job, true),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data): array
    {
        $job = DB::transaction(function () use ($id, $data): BossJob {
            $job = BossJob::query()->findOrFail($id);
            $job->fill($this->payload($data))->save();

            return $job->fresh();
        });

        return [
            'message' => '修改成功',
            'job' => $this->transform($job, true),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id): array
    {
        BossJob::query()->findOrFail($id)->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @param  list<string>  $ids
     * @return array<string, string>
     */
    public function batchDelete(array $ids): array
    {
        BossJob::query()->whereIn('id', $ids)->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function changeStatus(string $id, array $data): array
    {
        $job = BossJob::query()->findOrFail($id);

        if (array_key_exists('is_hot', $data)) {
            $job->is_hot = (int) $data['is_hot'];
        }

        if (array_key_exists('job_status', $data)) {
            $status = (int) $data['job_status'];
            $this->assertPublishable($status, [
                'job_title' => $job->job_title,
            ]);
            $job->job_status = $status;
        }

        $job->save();

        return [
            'message' => '状态已更新',
            'job' => $this->transform($job->fresh()),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function publicPaginate(array $filters): array
    {
        $paginator = BossJob::query()
            ->published()
            ->when(($filters['keyword'] ?? '') !== '', fn (Builder $query) => $query->where('job_title', 'like', '%'.$filters['keyword'].'%'))
            ->when(($filters['department'] ?? '') !== '', fn (Builder $query) => $query->where('department', 'like', '%'.$filters['department'].'%'))
            ->when(($filters['workplace'] ?? '') !== '', fn (Builder $query) => $query->where('workplace', 'like', '%'.$filters['workplace'].'%'))
            ->when(isset($filters['is_hot']) && $filters['is_hot'] !== '', fn (Builder $query) => $query->where('is_hot', $filters['is_hot']))
            ->orderByDesc('is_hot')
            ->orderByDesc('job_sort')
            ->orderByDesc('id')
            ->paginate((int) ($filters['per_page'] ?? 10));

        return [
            'data' => collect($paginator->items())->map(fn (BossJob $job) => $this->transform($job))->values(),
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
    public function publicFind(string $id): array
    {
        $job = BossJob::query()->published()->find($id);

        if (! $job) {
            throw (new ModelNotFoundException)->setModel(BossJob::class, [$id]);
        }

        $job->increment('view_count');

        return [
            'job' => $this->transform($job->fresh(), true),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        $status = (int) ($data['job_status'] ?? JobStatus::Pending->value);
        $this->assertPublishable($status, $data);

        return [
            'job_title' => (string) ($data['job_title'] ?? ''),
            'department' => (string) ($data['department'] ?? ''),
            'workplace' => (string) ($data['workplace'] ?? ''),
            'experience' => (string) ($data['experience'] ?? ''),
            'education' => (string) ($data['education'] ?? ''),
            'salary_range' => (string) ($data['salary_range'] ?? ''),
            'description' => (string) ($data['description'] ?? ''),
            'requirements' => (string) ($data['requirements'] ?? ''),
            'benefits' => (string) ($data['benefits'] ?? ''),
            'is_hot' => (int) ($data['is_hot'] ?? 0),
            'job_status' => $status,
            'expire_at' => ($data['expire_at'] ?? null) ?: null,
            'job_sort' => (int) ($data['job_sort'] ?? 0),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function assertPublishable(int $status, array $data): void
    {
        if ($status !== JobStatus::Published->value) {
            return;
        }

        if (trim((string) ($data['job_title'] ?? '')) === '') {
            throw ValidationException::withMessages([
                'job_title' => ['发布职位必须填写职位名称'],
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(BossJob $job, bool $detail = false): array
    {
        $status = $job->job_status instanceof JobStatus
            ? $job->job_status
            : JobStatus::from((int) $job->job_status);

        $data = [
            'id' => (string) $job->id,
            'job_title' => $job->job_title,
            'department' => $job->department,
            'workplace' => $job->workplace,
            'experience' => $job->experience,
            'education' => $job->education,
            'salary_range' => $job->salary_range,
            'is_hot' => (int) $job->is_hot,
            'job_status' => $status->value,
            'job_status_label' => $status->label(),
            'expire_at' => $job->expire_at?->toDateTimeString(),
            'view_count' => (int) $job->view_count,
            'job_sort' => (int) $job->job_sort,
            'created_at' => $job->created_at?->toDateTimeString(),
        ];

        if ($detail) {
            $data['description'] = $job->description;
            $data['requirements'] = $job->requirements;
            $data['benefits'] = $job->benefits;
        }

        return $data;
    }
}
