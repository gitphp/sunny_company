<?php

namespace App\Services;

use App\Models\HrDepartment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DepartmentService
{
    /**
     * @return array<string, mixed>
     */
    public function tree(): array
    {
        return [
            'departments' => HrDepartment::buildTree(HrDepartment::ordered()),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data, string $operatorId): array
    {
        $department = DB::transaction(function () use ($data, $operatorId): HrDepartment {
            $department = new HrDepartment($this->payload($data));
            $department->fillHierarchy($this->parent($data));
            $department->created_by = $operatorId ?: 0;
            $department->save();

            return $department;
        });

        return [
            'message' => '新增成功',
            'department' => $this->transform($department),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data): array
    {
        $department = DB::transaction(function () use ($id, $data): HrDepartment {
            $department = HrDepartment::query()->findOrFail($id);
            $parent = $this->parent($data);

            if ($parent && $this->isSelfOrDescendant($department, $parent)) {
                throw ValidationException::withMessages([
                    'parent_id' => ['不能选择自己或下级作为父部门'],
                ]);
            }

            $department->fill($this->payload($data));
            $department->fillHierarchy($parent);
            $department->save();

            return $department->fresh();
        });

        return [
            'message' => '修改成功',
            'department' => $this->transform($department),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id): array
    {
        $department = HrDepartment::query()->findOrFail($id);

        if ($department->children()->exists()) {
            throw ValidationException::withMessages([
                'id' => ['请先删除下级部门'],
            ]);
        }

        $department->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        return [
            'dept_name' => (string) ($data['dept_name'] ?? ''),
            'dept_code' => (string) ($data['dept_code'] ?? ''),
            'leader_user_id' => ($data['leader_user_id'] ?? 0) ?: 0,
            'dept_phone' => (string) ($data['dept_phone'] ?? ''),
            'dept_sort' => (int) ($data['dept_sort'] ?? 0),
            'dept_status' => (int) ($data['dept_status'] ?? 1),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function parent(array $data): ?HrDepartment
    {
        $parentId = (string) ($data['parent_id'] ?? '0');

        if ($parentId === '' || $parentId === '0') {
            return null;
        }

        return HrDepartment::query()->findOrFail($parentId);
    }

    private function isSelfOrDescendant(HrDepartment $department, HrDepartment $parent): bool
    {
        return (string) $parent->id === (string) $department->id
            || str_contains(','.$parent->ancestors.',', ','.$department->id.',');
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(HrDepartment $department): array
    {
        return [
            'id' => (string) $department->id,
            'parent_id' => (string) $department->parent_id,
            'dept_name' => $department->dept_name,
            'dept_code' => $department->dept_code,
            'ancestors' => $department->ancestors,
            'dept_level' => $department->dept_level,
            'leader_user_id' => (string) $department->leader_user_id,
            'dept_phone' => $department->dept_phone,
            'dept_sort' => $department->dept_sort,
            'dept_status' => $department->dept_status,
        ];
    }
}
