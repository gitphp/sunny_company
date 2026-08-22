<?php

namespace App\Providers;

use App\Models\AuthRole;
use App\Models\HrDepartment;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Route::bind('role', fn (string $value) => AuthRole::query()->whereKey($value)->firstOrFail());
        Route::bind('department', fn (string $value) => HrDepartment::query()->whereKey($value)->firstOrFail());
    }
}
