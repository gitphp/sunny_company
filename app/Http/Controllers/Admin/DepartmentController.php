<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDepartmentRequest;
use App\Http\Requests\Admin\UpdateDepartmentRequest;
use App\Models\HrDepartment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'departments' => HrDepartment::buildTree(HrDepartment::ordered()),
        ]);
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $department = new HrDepartment($this->payload($request));
        $department->fillHierarchy($this->parent($request));
        $department->created_by = (string) ($request->user()?->id ?? 0);
        $department->save();

        return response()->json([
            'message' => '新增成功',
            'department' => $this->transform($department),
        ], 201);
    }

    public function update(UpdateDepartmentRequest $request, HrDepartment $department): JsonResponse
    {
        $parent = $this->parent($request);

        if ($parent && ((string) $parent->id === (string) $department->id || str_contains(','.$parent->ancestors.',', ','.$department->id.','))) {
            abort(422, '不能选择自己或下级作为父部门');
        }

        $department->fill($this->payload($request));
        $department->fillHierarchy($parent);
        $department->save();

        return response()->json([
            'message' => '修改成功',
            'department' => $this->transform($department->fresh()),
        ]);
    }

    public function destroy(HrDepartment $department): JsonResponse
    {
        if ($department->children()->exists()) {
            abort(422, '请先删除下级部门');
        }

        $department->delete();

        return response()->json([
            'message' => '删除成功',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request): array
    {
        return [
            'dept_name' => $request->string('dept_name')->toString(),
            'dept_code' => $request->string('dept_code')->toString(),
            'leader_user_id' => $request->input('leader_user_id', 0) ?: 0,
            'dept_phone' => $request->string('dept_phone')->toString(),
            'dept_sort' => $request->integer('dept_sort', 0),
            'dept_status' => $request->integer('dept_status', 1),
        ];
    }

    private function parent(Request $request): ?HrDepartment
    {
        $parentId = (string) $request->input('parent_id', '0');

        if ($parentId === '' || $parentId === '0') {
            return null;
        }

        return HrDepartment::query()->findOrFail($parentId);
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
