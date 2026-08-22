<?php

namespace App\Http\Resources\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'user_name' => $this->user_name,
            'real_name' => $this->real_name,
            'user_mobile' => $this->user_mobile,
            'user_email' => $this->user_email,
            'user_status' => $this->user_status?->value,
            'user_status_label' => $this->user_status?->label(),
            'lock_reason' => $this->lock_reason,
            'lock_expire_time' => $this->lock_expire_time?->toDateTimeString(),
            'last_login_ip' => $this->last_login_ip,
            'last_login_region' => $this->last_login_region,
            'last_login_at' => $this->last_login_at?->toDateTimeString(),
            'register_ip' => $this->register_ip,
            'register_device' => $this->register_device,
            'register_channel' => $this->register_channel,
            'real_auth_status' => $this->real_auth_status?->value,
            'real_auth_status_label' => $this->real_auth_status?->label(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
