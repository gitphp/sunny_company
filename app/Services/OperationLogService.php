<?php

namespace App\Services;

use App\Models\OperationLog;
use Illuminate\Database\Eloquent\Builder;

class OperationLogService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters): array
    {
        $paginator = OperationLog::query()
            ->when(($filters['operator_name'] ?? '') !== '', fn (Builder $query) => $query->where('operator_name', 'like', '%'.$filters['operator_name'].'%'))
            ->when(($filters['biz_type'] ?? '') !== '', fn (Builder $query) => $query->where('biz_type', $filters['biz_type']))
            ->when(($filters['action'] ?? '') !== '', fn (Builder $query) => $query->where('action', $filters['action']))
            ->when(isset($filters['operator_status']) && $filters['operator_status'] !== '', fn (Builder $query) => $query->where('operator_status', $filters['operator_status']))
            ->when(($filters['begin_time'] ?? '') !== '', fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['begin_time']))
            ->when(($filters['end_time'] ?? '') !== '', fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['end_time']))
            ->orderByDesc('created_at')
            ->paginate((int) ($filters['per_page'] ?? 10));

        return [
            'data' => collect($paginator->items())->map(fn (OperationLog $log) => $this->transform($log))->values(),
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
            'log' => $this->transform(OperationLog::query()->findOrFail($id), true),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id): array
    {
        OperationLog::query()->findOrFail($id)->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @param  list<string>  $ids
     * @return array<string, string>
     */
    public function batchDelete(array $ids): array
    {
        OperationLog::query()->whereIn('id', $ids)->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function write(array $payload): void
    {
        OperationLog::query()->create([
            'operator_id' => $payload['operator_id'] ?? 0,
            'operator_name' => mb_substr((string) ($payload['operator_name'] ?? ''), 0, 50),
            'biz_type' => mb_substr((string) ($payload['biz_type'] ?? ''), 0, 16),
            'activity_type' => mb_substr((string) ($payload['activity_type'] ?? ''), 0, 32),
            'action' => mb_substr((string) ($payload['action'] ?? ''), 0, 16),
            'biz_id' => $payload['biz_id'] ?? 0,
            'biz_label' => mb_substr((string) ($payload['biz_label'] ?? ''), 0, 128),
            'old_value' => $payload['old_value'] ?? null,
            'new_value' => $payload['new_value'] ?? null,
            'operator_status' => (int) ($payload['operator_status'] ?? 1),
            'error_msg' => mb_substr((string) ($payload['error_msg'] ?? ''), 0, 2048),
            'client_ip' => mb_substr((string) ($payload['client_ip'] ?? ''), 0, 32),
            'user_agent' => mb_substr((string) ($payload['user_agent'] ?? ''), 0, 255),
            'request_url' => mb_substr((string) ($payload['request_url'] ?? ''), 0, 255),
            'method_fun' => mb_substr((string) ($payload['method_fun'] ?? ''), 0, 128),
            'created_at' => now(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(OperationLog $log, bool $detail = false): array
    {
        $data = [
            'id' => (string) $log->id,
            'operator_id' => (string) $log->operator_id,
            'operator_name' => $log->operator_name,
            'biz_type' => $log->biz_type,
            'activity_type' => $log->activity_type,
            'action' => $log->action,
            'biz_id' => (string) $log->biz_id,
            'biz_label' => $log->biz_label,
            'operator_status' => (int) $log->operator_status,
            'error_msg' => $log->error_msg,
            'client_ip' => $log->client_ip,
            'request_url' => $log->request_url,
            'method_fun' => $log->method_fun,
            'created_at' => $log->created_at?->format('Y-m-d H:i:s.u'),
        ];

        if ($detail) {
            $data['old_value'] = $log->old_value;
            $data['new_value'] = $log->new_value;
            $data['user_agent'] = $log->user_agent;
        }

        return $data;
    }
}
