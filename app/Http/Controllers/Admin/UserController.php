<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RealAuthStatus;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\UserIndexRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\AuthRole;
use App\Models\HrDepartment;
use App\Models\User;
use App\Services\RbacService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function __construct(private readonly RbacService $rbac) {}

    public function index(UserIndexRequest $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 10);

        $paginator = $this->filteredQuery($request)
            ->with(['department', 'roles'])
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'data' => UserResource::collection($paginator->items())->resolve(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::query()->create($this->payload($request, true));
        $this->syncRoles($user, $request);

        return response()->json([
            'message' => '新增成功',
            'user' => UserResource::make($user)->resolve(),
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'user' => UserResource::make($user->load(['department', 'roles']))->resolve(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user->fill($this->payload($request, false));
        $user->save();
        $this->syncRoles($user, $request);

        return response()->json([
            'message' => '修改成功',
            'user' => UserResource::make($user->fresh())->resolve(),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->guardSelf($user);
        $user->delete();

        return response()->json([
            'message' => '删除成功',
        ]);
    }

    public function batchDestroy(Request $request): JsonResponse
    {
        $ids = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'string'],
        ])['ids'];

        $currentId = (string) $request->user()->id;
        $ids = array_values(array_filter($ids, fn (string $id): bool => $id !== $currentId));

        User::query()->whereIn('id', $ids)->delete();

        return response()->json([
            'message' => '删除成功',
        ]);
    }

    public function changeStatus(Request $request, User $user): JsonResponse
    {
        $this->guardSelf($user);

        $validated = $request->validate([
            'user_status' => ['required', 'integer', 'in:0,1'],
        ]);

        $user->forceFill([
            'user_status' => $validated['user_status'],
            'lock_reason' => $validated['user_status'] === UserStatus::Normal->value ? '' : $user->lock_reason,
            'lock_expire_time' => $validated['user_status'] === UserStatus::Normal->value ? null : $user->lock_expire_time,
        ])->save();

        return response()->json([
            'message' => '状态已更新',
            'user' => UserResource::make($user->fresh())->resolve(),
        ]);
    }

    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'max:64'],
        ]);

        $user->forceFill([
            'password_hash' => $validated['password'],
        ])->save();

        return response()->json([
            'message' => '密码已重置',
        ]);
    }

    public function export(UserIndexRequest $request): StreamedResponse
    {
        $users = $this->filteredQuery($request)->orderByDesc('created_at')->get();

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
                    optional($user->created_at)->toDateTimeString(),
                ]);
            }

            fclose($handle);
        }, 'user_account.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function filteredQuery(UserIndexRequest $request): Builder
    {
        $query = User::query()
            ->when($request->filled('user_name'), fn (Builder $query) => $query->where('user_name', 'like', '%'.$request->string('user_name').'%'))
            ->when($request->filled('user_mobile'), fn (Builder $query) => $query->where('user_mobile', 'like', '%'.$request->string('user_mobile').'%'))
            ->when($request->filled('user_status'), fn (Builder $query) => $query->where('user_status', $request->integer('user_status')))
            ->when($request->filled('begin_time'), fn (Builder $query) => $query->whereDate('created_at', '>=', $request->date('begin_time')))
            ->when($request->filled('end_time'), fn (Builder $query) => $query->whereDate('created_at', '<=', $request->date('end_time')))
            ->when($request->filled('dept_id') && $request->string('dept_id')->toString() !== '0', function (Builder $query) use ($request): void {
                $query->whereIn('dept_id', HrDepartment::selfAndDescendantIds($request->string('dept_id')->toString()));
            });

        return $this->rbac->applyDataScope($query, $request->user());
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request, bool $creating): array
    {
        $data = [
            'user_name' => $request->string('user_name')->toString(),
            'real_name' => $request->string('real_name')->toString(),
            'user_mobile' => $request->string('user_mobile')->toString(),
            'user_email' => $request->string('user_email')->toString(),
            'user_status' => $request->integer('user_status', UserStatus::Normal->value),
            'real_auth_status' => $request->integer('real_auth_status', RealAuthStatus::Unverified->value),
            'register_channel' => $request->string('register_channel', 'web')->toString(),
            'lock_reason' => $request->string('lock_reason')->toString(),
            'lock_expire_time' => $request->input('lock_expire_time'),
            'dept_id' => $request->input('dept_id') ?: 0,
        ];

        if ($creating) {
            $data['password_hash'] = $request->string('password')->toString();
            $data['password_salt'] = '';
            $data['register_ip'] = (string) $request->ip();
            $data['register_device'] = mb_substr((string) $request->userAgent(), 0, 128);
        } elseif ($request->filled('password')) {
            $data['password_hash'] = $request->string('password')->toString();
        }

        return $data;
    }

    private function syncRoles(User $user, Request $request): void
    {
        if (! $request->exists('role_ids')) {
            return;
        }

        $ids = array_map('strval', $request->input('role_ids', []));
        $superId = (string) AuthRole::query()->where('role_code', AuthRole::SUPER_ADMIN_CODE)->value('id');

        if ($superId !== '' && ! $this->rbac->isSuperAdmin($request->user())) {
            $ids = array_values(array_filter($ids, fn (string $id): bool => $id !== $superId));
        }

        $user->roles()->sync($this->rbac->syncMap($ids));
    }

    private function guardSelf(User $user): void
    {
        if ((string) $user->id === (string) request()->user()?->id) {
            abort(422, '不能操作当前登录账号');
        }
    }
}
