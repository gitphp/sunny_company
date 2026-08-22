<?php

/**
 * Web 路由定义
 *
 * @package     Routes
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

use App\Http\Controllers\AdController;
use App\Http\Controllers\Admin\AdMaterialController;
use App\Http\Controllers\Admin\AdPositionController;
use App\Http\Controllers\Admin\AiController;
use App\Http\Controllers\Admin\AiProviderController;
use App\Http\Controllers\Admin\ArticleCategoryController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\FriendLinkController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OperationLogController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProductBrandController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductSpecificationController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SiteConfigController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\FeedbackController as PublicFeedbackController;
use App\Http\Controllers\JobController as PublicJobController;
use App\Http\Controllers\ProductController as PublicProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::post('/feedbacks', [PublicFeedbackController::class, 'store'])->middleware('throttle:8,1');
    Route::get('/ads/{code}', [AdController::class, 'show']);
    Route::get('/jobs', [PublicJobController::class, 'index']);
    Route::get('/jobs/{job}', [PublicJobController::class, 'show']);
    Route::get('/products', [PublicProductController::class, 'index']);
    Route::get('/products/{product}', [PublicProductController::class, 'show']);

    Route::prefix('admin')->group(function () {
        Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:10,1');

        Route::middleware(['auth', 'operlog'])->group(function () {
            Route::post('/logout', [AdminAuthController::class, 'logout']);
            Route::get('/user', [AdminAuthController::class, 'user']);
            Route::get('/menus', [AdminAuthController::class, 'menus']);
            Route::get('/dashboard', [DashboardController::class, 'index']);

            Route::get('/ai/providers/options', [AiProviderController::class, 'options'])->middleware('permission:ai:chat');
            Route::get('/ai/providers', [AiProviderController::class, 'index'])->middleware('permission:ai:config');
            Route::post('/ai/providers', [AiProviderController::class, 'store'])->middleware('permission:ai:config');
            Route::put('/ai/providers/{provider}/status', [AiProviderController::class, 'changeStatus'])->middleware('permission:ai:config');
            Route::put('/ai/providers/{provider}/default', [AiProviderController::class, 'setDefault'])->middleware('permission:ai:config');
            Route::post('/ai/providers/{provider}/test', [AiProviderController::class, 'test'])->middleware('permission:ai:config');
            Route::put('/ai/providers/{provider}', [AiProviderController::class, 'update'])->middleware('permission:ai:config');
            Route::delete('/ai/providers/{provider}', [AiProviderController::class, 'destroy'])->middleware('permission:ai:config');
            Route::post('/ai/chat', [AiController::class, 'chat'])->middleware(['permission:ai:chat', 'throttle:30,1']);

            Route::get('/options/roles', [OptionController::class, 'roles']);
            Route::get('/options/departments', [OptionController::class, 'departments']);
            Route::get('/options/posts', [OptionController::class, 'posts']);
            Route::get('/options/article-categories', [OptionController::class, 'articleCategories']);
            Route::get('/options/ad-positions', [OptionController::class, 'adPositions']);
            Route::get('/options/product-brands', [OptionController::class, 'productBrands']);
            Route::get('/options/product-categories', [OptionController::class, 'productCategories']);
            Route::get('/options/product-specs', [OptionController::class, 'productSpecs']);
            Route::get('/options/menus', [MenuController::class, 'index']);

            Route::get('/users/export', [UserController::class, 'export'])->middleware('permission:system:user:export');
            Route::post('/users/batch-delete', [UserController::class, 'batchDestroy'])->middleware('permission:system:user:remove');
            Route::put('/users/{user}/status', [UserController::class, 'changeStatus'])->middleware('permission:system:user:edit');
            Route::put('/users/{user}/password', [UserController::class, 'resetPassword'])->middleware('permission:system:user:resetPwd');
            Route::get('/users', [UserController::class, 'index'])->middleware('permission:system:user:list');
            Route::post('/users', [UserController::class, 'store'])->middleware('permission:system:user:add');
            Route::get('/users/{user}', [UserController::class, 'show'])->middleware('permission:system:user:list');
            Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:system:user:edit');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:system:user:remove');

            Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:system:role:list');
            Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:system:role:add');
            Route::get('/roles/{role}', [RoleController::class, 'show'])->middleware('permission:system:role:list');
            Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:system:role:edit');
            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:system:role:remove');
            Route::put('/roles/{role}/status', [RoleController::class, 'changeStatus'])->middleware('permission:system:role:edit');

            Route::get('/departments', [DepartmentController::class, 'index'])->middleware('permission:system:dept:list');
            Route::post('/departments', [DepartmentController::class, 'store'])->middleware('permission:system:dept:add');
            Route::put('/departments/{department}', [DepartmentController::class, 'update'])->middleware('permission:system:dept:edit');
            Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->middleware('permission:system:dept:remove');

            Route::get('/posts', [PostController::class, 'index'])->middleware('permission:system:post:list');
            Route::post('/posts', [PostController::class, 'store'])->middleware('permission:system:post:add');
            Route::put('/posts/{post}', [PostController::class, 'update'])->middleware('permission:system:post:edit');
            Route::delete('/posts/{post}', [PostController::class, 'destroy'])->middleware('permission:system:post:remove');

            Route::get('/article-categories', [ArticleCategoryController::class, 'index'])->middleware('permission:cms:category:list');
            Route::post('/article-categories', [ArticleCategoryController::class, 'store'])->middleware('permission:cms:category:add');
            Route::put('/article-categories/{category}', [ArticleCategoryController::class, 'update'])->middleware('permission:cms:category:edit');
            Route::delete('/article-categories/{category}', [ArticleCategoryController::class, 'destroy'])->middleware('permission:cms:category:remove');

            Route::post('/articles/batch-delete', [ArticleController::class, 'batchDestroy'])->middleware('permission:cms:article:remove');
            Route::put('/articles/{article}/status', [ArticleController::class, 'changeStatus'])->middleware('permission:cms:article:edit');
            Route::get('/articles', [ArticleController::class, 'index'])->middleware('permission:cms:article:list');
            Route::post('/articles', [ArticleController::class, 'store'])->middleware('permission:cms:article:add');
            Route::get('/articles/{article}', [ArticleController::class, 'show'])->middleware('permission:cms:article:list');
            Route::put('/articles/{article}', [ArticleController::class, 'update'])->middleware('permission:cms:article:edit');
            Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->middleware('permission:cms:article:remove');

            Route::get('/system/menus', [MenuController::class, 'index'])->middleware('permission:system:menu:list');
            Route::get('/permissions', [PermissionController::class, 'index']);

            Route::get('/site-configs', [SiteConfigController::class, 'index'])->middleware('permission:cms:config:list');
            Route::put('/site-configs', [SiteConfigController::class, 'update'])->middleware('permission:cms:config:edit');

            Route::get('/ad-positions', [AdPositionController::class, 'index'])->middleware('permission:cms:ad-position:list');
            Route::post('/ad-positions', [AdPositionController::class, 'store'])->middleware('permission:cms:ad-position:add');
            Route::put('/ad-positions/{position}/status', [AdPositionController::class, 'changeStatus'])->middleware('permission:cms:ad-position:edit');
            Route::put('/ad-positions/{position}', [AdPositionController::class, 'update'])->middleware('permission:cms:ad-position:edit');
            Route::delete('/ad-positions/{position}', [AdPositionController::class, 'destroy'])->middleware('permission:cms:ad-position:remove');

            Route::post('/jobs/batch-delete', [JobController::class, 'batchDestroy'])->middleware('permission:cms:job:remove');
            Route::put('/jobs/{job}/status', [JobController::class, 'changeStatus'])->middleware('permission:cms:job:edit');
            Route::get('/jobs', [JobController::class, 'index'])->middleware('permission:cms:job:list');
            Route::post('/jobs', [JobController::class, 'store'])->middleware('permission:cms:job:add');
            Route::get('/jobs/{job}', [JobController::class, 'show'])->middleware('permission:cms:job:list');
            Route::put('/jobs/{job}', [JobController::class, 'update'])->middleware('permission:cms:job:edit');
            Route::delete('/jobs/{job}', [JobController::class, 'destroy'])->middleware('permission:cms:job:remove');

            Route::get('/ad-materials', [AdMaterialController::class, 'index'])->middleware('permission:cms:ad-material:list');
            Route::post('/ad-materials', [AdMaterialController::class, 'store'])->middleware('permission:cms:ad-material:add');
            Route::put('/ad-materials/{material}/status', [AdMaterialController::class, 'changeStatus'])->middleware('permission:cms:ad-material:edit');
            Route::put('/ad-materials/{material}', [AdMaterialController::class, 'update'])->middleware('permission:cms:ad-material:edit');
            Route::delete('/ad-materials/{material}', [AdMaterialController::class, 'destroy'])->middleware('permission:cms:ad-material:remove');

            Route::get('/friend-links', [FriendLinkController::class, 'index'])->middleware('permission:cms:link:list');
            Route::post('/friend-links', [FriendLinkController::class, 'store'])->middleware('permission:cms:link:add');
            Route::put('/friend-links/{link}/status', [FriendLinkController::class, 'changeStatus'])->middleware('permission:cms:link:edit');
            Route::put('/friend-links/{link}', [FriendLinkController::class, 'update'])->middleware('permission:cms:link:edit');
            Route::delete('/friend-links/{link}', [FriendLinkController::class, 'destroy'])->middleware('permission:cms:link:remove');

            Route::post('/feedbacks/batch-delete', [FeedbackController::class, 'batchDestroy'])->middleware('permission:cms:feedback:remove');
            Route::put('/feedbacks/{feedback}/reply', [FeedbackController::class, 'reply'])->middleware('permission:cms:feedback:reply');
            Route::put('/feedbacks/{feedback}/status', [FeedbackController::class, 'changeStatus'])->middleware('permission:cms:feedback:reply');
            Route::get('/feedbacks', [FeedbackController::class, 'index'])->middleware('permission:cms:feedback:list');
            Route::get('/feedbacks/{feedback}', [FeedbackController::class, 'show'])->middleware('permission:cms:feedback:list');
            Route::delete('/feedbacks/{feedback}', [FeedbackController::class, 'destroy'])->middleware('permission:cms:feedback:remove');

            Route::post('/operation-logs/batch-delete', [OperationLogController::class, 'batchDestroy'])->middleware('permission:system:operlog:remove');
            Route::get('/operation-logs', [OperationLogController::class, 'index'])->middleware('permission:system:operlog:list');
            Route::get('/operation-logs/{log}', [OperationLogController::class, 'show'])->middleware('permission:system:operlog:list');
            Route::delete('/operation-logs/{log}', [OperationLogController::class, 'destroy'])->middleware('permission:system:operlog:remove');

            Route::get('/product-brands', [ProductBrandController::class, 'index'])->middleware('permission:product:brand:list');
            Route::post('/product-brands', [ProductBrandController::class, 'store'])->middleware('permission:product:brand:add');
            Route::put('/product-brands/{brand}/status', [ProductBrandController::class, 'changeStatus'])->middleware('permission:product:brand:edit');
            Route::put('/product-brands/{brand}', [ProductBrandController::class, 'update'])->middleware('permission:product:brand:edit');
            Route::delete('/product-brands/{brand}', [ProductBrandController::class, 'destroy'])->middleware('permission:product:brand:remove');

            Route::get('/product-categories', [ProductCategoryController::class, 'index'])->middleware('permission:product:category:list');
            Route::post('/product-categories', [ProductCategoryController::class, 'store'])->middleware('permission:product:category:add');
            Route::put('/product-categories/{category}', [ProductCategoryController::class, 'update'])->middleware('permission:product:category:edit');
            Route::delete('/product-categories/{category}', [ProductCategoryController::class, 'destroy'])->middleware('permission:product:category:remove');

            Route::get('/product-specs', [ProductSpecificationController::class, 'index'])->middleware('permission:product:spec:list');
            Route::post('/product-specs', [ProductSpecificationController::class, 'store'])->middleware('permission:product:spec:add');
            Route::put('/product-specs/{spec}/status', [ProductSpecificationController::class, 'changeStatus'])->middleware('permission:product:spec:edit');
            Route::post('/product-specs/{spec}/values', [ProductSpecificationController::class, 'createValue'])->middleware('permission:product:spec:add');
            Route::put('/product-specs/{spec}/values/{value}', [ProductSpecificationController::class, 'updateValue'])->middleware('permission:product:spec:edit');
            Route::delete('/product-specs/{spec}/values/{value}', [ProductSpecificationController::class, 'destroyValue'])->middleware('permission:product:spec:remove');
            Route::put('/product-specs/{spec}', [ProductSpecificationController::class, 'update'])->middleware('permission:product:spec:edit');
            Route::delete('/product-specs/{spec}', [ProductSpecificationController::class, 'destroy'])->middleware('permission:product:spec:remove');

            Route::post('/products/batch-delete', [ProductController::class, 'batchDestroy'])->middleware('permission:product:remove');
            Route::put('/products/{product}/status', [ProductController::class, 'changeStatus'])->middleware('permission:product:edit');
            Route::get('/products', [ProductController::class, 'index'])->middleware('permission:product:list');
            Route::post('/products', [ProductController::class, 'store'])->middleware('permission:product:add');
            Route::get('/products/{product}', [ProductController::class, 'show'])->middleware('permission:product:list');
            Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('permission:product:edit');
            Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('permission:product:remove');
        });
    });
});

Route::redirect('/', '/admin');
Route::redirect('/login', '/admin/login');
Route::redirect('/dashboard', '/admin/index');
Route::redirect('/register', '/admin/login');
Route::view('/admin/{any?}', 'backend')->where('any', '.*');
