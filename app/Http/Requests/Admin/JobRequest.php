<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class JobRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [
            'job_title' => ['nullable', 'string', 'max:64'],
            'department' => ['nullable', 'string', 'max:64'],
            'job_status' => ['nullable', 'integer', 'in:1,2,3'],
            'is_hot' => ['nullable', 'integer', 'in:0,1'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function storeRules(): array
    {
        return $this->jobRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function updateRules(): array
    {
        return $this->jobRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function batchDestroyRules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatusRules(): array
    {
        return [
            'job_status' => ['nullable', 'integer', 'in:1,2,3'],
            'is_hot' => ['nullable', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function jobRules(): array
    {
        return [
            'job_title' => ['required', 'string', 'max:64'],
            'department' => ['nullable', 'string', 'max:64'],
            'workplace' => ['nullable', 'string', 'max:128'],
            'experience' => ['nullable', 'string', 'max:64'],
            'education' => ['nullable', 'string', 'max:64'],
            'salary_range' => ['nullable', 'string', 'max:64'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'is_hot' => ['nullable', 'integer', 'in:0,1'],
            'job_status' => ['nullable', 'integer', 'in:1,2,3'],
            'expire_at' => ['nullable', 'date'],
            'job_sort' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'job_title' => '职位名称',
            'department' => '所属部门',
            'workplace' => '工作地点',
        ];
    }
}
