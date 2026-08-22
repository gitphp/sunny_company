<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_name' => ['nullable', 'string', 'max:32'],
            'user_mobile' => ['nullable', 'string', 'max:16'],
            'user_status' => ['nullable', 'integer', 'in:0,1,2,3'],
            'begin_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:begin_time'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
