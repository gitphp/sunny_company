<?php

use App\Http\Controllers\Admin\ArticleCategoryController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\FriendLinkController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OperationLogController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SiteConfigController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\FeedbackController as PublicFeedbackController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::post('/feedbacks', [PublicFeedbackController::class, 'store'])->middleware('throttle:8,1');

    Route::prefix('admin')->group(function () {
        Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:10,1');

        Route::middleware(['auth', 'operlog'])->group(function () {
            Route::post('/logout', [AdminAuthController::class, 'logout']);
            Route::get('/user', [AdminAuthController::class, 'user']);
            Route::get('/menus', [AdminAuthController::class, 'menus']);

            Route::get('/options/roles', [OptionController::class, 'roles']);
            Route::get('/options/departments', [OptionController::class, 'departments']);
            Route::get('/options/posts', [OptionController::class, 'posts']);
            Route::get('/options/article-categories', [OptionController::class, 'articleCategories']);
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
        });
    });
});

Route::redirect('/', '/admin');
Route::redirect('/login', '/admin/login');
Route::redirect('/dashboard', '/admin/index');
Route::redirect('/register', '/admin/login');
Route::view('/admin/{any?}', 'backend')->where('any', '.*');
