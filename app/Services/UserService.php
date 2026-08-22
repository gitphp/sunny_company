<?php

namespace App\Services;

use App\Enums\RealAuthStatus;
use App\Enums\UserStatus;
use App\Http\Resources\Admin\UserResource;
use App\Models\AuthRole;
use App\Models\HrDepartment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserService
{
    public function __construct(private readonly RbacService $rbac) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters, User $operator): array
    {
        $paginator = $this->filteredQuery($filters, $operator)
            ->with(['department', 'roles'])
            ->orderByDesc('created_at')
            ->paginate((int) ($filters['per_page'] ?? 10));

        return [
            'data' => UserResource::collection($paginator->items())->resolve(),
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
     * @param  array{ip?: string, user_agent?: string}  $context
     * @return array<string, mixed>
     */
    public function create(array $data, User $operator, array $context = []): array
    {
        $user = DB::transaction(function () use ($data, $operator, $context): User {
            $user = User::query()->create($this->payload($data, true, $context));
            $this->syncRoles($user, $data, $operator);

            return $user->load(['department', 'roles']);
        });

        return [
            'message' => '新增成功',
            'user' => UserResource::make($user)->resolve(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function find(string $id): array
    {
        $user = User::query()->with(['department', 'roles'])->findOrFail($id);

        return [
            'user' => UserResource::make($user)->resolve(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data, User $operator): array
    {
        $user = DB::transaction(function () use ($id, $data, $operator): User {
            $user = User::query()->findOrFail($id);
            $user->fill($this->payload($data, false));
            $user->save();
            $this->syncRoles($user, $data, $operator);

            return $user->fresh(['department', 'roles']);
        });

        return [
            'message' => '修改成功',
            'user' => UserResource::make($user)->resolve(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id, User $operator): array
    {
        $user = User::query()->findOrFail($id);
        $this->assertNotSelf($user, $operator);
        $user->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @param  list<string>  $ids
     * @return array<string, string>
     */
    public function batchDelete(array $ids, User $operator): array
    {
        $ids = array_values(array_filter($ids, fn (string $id): bool => $id !== (string) $operator->id));
        User::query()->whereIn('id', $ids)->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatus(string $id, int $status, User $operator): array
    {
        $user = User::query()->findOrFail($id);
        $this->assertNotSelf($user, $operator);

        $user->forceFill([
            'user_status' => $status,
            'lock_reason' => $status === UserStatus::Normal->value ? '' : $user->lock_reason,
            'lock_expire_time' => $status === UserStatus::Normal->value ? null : $user->lock_expire_time,
        ])->save();

        return [
            'message' => '状态已更新',
            'user' => UserResource::make($user->fresh(['department', 'roles']))->resolve(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function resetPassword(string $id, string $password): array
    {
        $user = User::query()->findOrFail($id);
        $user->forceFill(['password_hash' => $password])->save();

        return ['message' => '密码已重置'];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function export(array $filters, User $operator): StreamedResponse
    {
        $users = $this->filteredQuery($filters, $operator)->orderByDesc('created_at')->get();

        return response()->streamDownload(function () use ($users): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['用户编号', '用户名称', '真实姓名', '手机号码', '邮箱', '状态', '实名状态', '创建时间']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    (string) $user->id,
                    $user->user_name,
                    $user->real_name,
                    $user->user_mobile,
                    $user->user_email,
                    $user->user_status?->label(),
                    $user->real_auth_status?->label(),
                    $user->created_at?->toDateTimeString(),
                ]);
            }

            fclose($handle);
        }, 'user_account.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function filteredQuery(array $filters, User $operator): Builder
    {
        $query = User::query()
            ->when(($filters['user_name'] ?? '') !== '', fn (Builder $query) => $query->where('user_name', 'like', '%'.$filters['user_name'].'%'))
            ->when(($filters['user_mobile'] ?? '') !== '', fn (Builder $query) => $query->where('user_mobile', 'like', '%'.$filters['user_mobile'].'%'))
            ->when(isset($filters['user_status']) && $filters['user_status'] !== '', fn (Builder $query) => $query->where('user_status', $filters['user_status']))
            ->when(($filters['begin_time'] ?? '') !== '', fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['begin_time']))
            ->when(($filters['end_time'] ?? '') !== '', fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['end_time']))
            ->when(($filters['dept_id'] ?? '') !== '' && ($filters['dept_id'] ?? '0') !== '0', function (Builder $query) use ($filters): void {
                $query->whereIn('dept_id', HrDepartment::selfAndDescendantIds((string) $filters['dept_id']));
            });

        return $this->rbac->applyDataScope($query, $operator);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array{ip?: string, user_agent?: string}  $context
     * @return array<string, mixed>
     */
    private function payload(array $data, bool $creating, array $context = []): array
    {
        $payload = [
            'user_name' => (string) ($data['user_name'] ?? ''),
            'real_name' => (string) ($data['real_name'] ?? ''),
            'user_mobile' => (string) ($data['user_mobile'] ?? ''),
            'user_email' => (string) ($data['user_email'] ?? ''),
            'user_status' => (int) ($data['user_status'] ?? UserStatus::Normal->value),
            'real_auth_status' => (int) ($data['real_auth_status'] ?? RealAuthStatus::Unverified->value),
            'register_channel' => (string) ($data['register_channel'] ?? 'web'),
            'lock_reason' => (string) ($data['lock_reason'] ?? ''),
            'lock_expire_time' => $data['lock_expire_time'] ?? null,
            'dept_id' => ($data['dept_id'] ?? 0) ?: 0,
        ];

        if ($creating) {
            $payload['password_hash'] = (string) ($data['password'] ?? '');
            $payload['password_salt'] = '';
            $payload['register_ip'] = (string) ($context['ip'] ?? '');
            $payload['register_device'] = mb_substr((string) ($context['user_agent'] ?? ''), 0, 128);
        } elseif (! empty($data['password'])) {
            $payload['password_hash'] = (string) $data['password'];
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncRoles(User $user, array $data, User $operator): void
    {
        if (! array_key_exists('role_ids', $data)) {
            return;
        }

        $ids = array_map('strval', $data['role_ids'] ?? []);
        $superId = (string) AuthRole::query()->where('role_code', AuthRole::SUPER_ADMIN_CODE)->value('id');

        if ($superId !== '' && ! $this->rbac->isSuperAdmin($operator)) {
            $ids = array_values(array_filter($ids, fn (string $id): bool => $id !== $superId));
        }

        $user->roles()->sync($this->rbac->syncMap($ids));
    }

    private function assertNotSelf(User $user, User $operator): void
    {
        if ((string) $user->id === (string) $operator->id) {
            throw ValidationException::withMessages([
                'id' => ['不能操作当前登录账号'],
            ]);
        }
    }
}
