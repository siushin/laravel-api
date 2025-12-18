<?php

use Modules\Base\Http\Controllers\DictionaryCategoryController;
use Modules\Base\Http\Controllers\DictionaryController;
use Modules\Base\Http\Controllers\FileController;
use Modules\Base\Http\Controllers\LogController;
use Modules\Base\Http\Controllers\MenuController;
use Modules\Base\Http\Controllers\OrganizationController;
use Modules\Base\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

// 公共路由
Route::get('/dictionary/getTplFile', [DictionaryController::class, 'getTplFile']);  // 下载数据字典模板

// 不需要认证的接口
Route::post('/login/account', [AccountController::class, 'login']);
Route::post('/login/code', [AccountController::class, 'loginByCode']);
Route::post('/resetPassword', [AccountController::class, 'resetPassword']);
Route::post('/register', [AccountController::class, 'register']);

// API鉴权 通用接口（不区分用户类型）
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/currentUser', [AccountController::class, 'getCurrentUserInfo']);
    Route::post('/refreshToken', [AccountController::class, 'refreshToken']);
    Route::post('/changePassword', [AccountController::class, 'changePassword']);
    Route::post('/logout', [AccountController::class, 'logout']);
    Route::post('/getUserMenus', [MenuController::class, 'getUserMenus']);
});

// API鉴权 管理员 路由组
Route::middleware(['auth:sanctum'])->prefix('/admin')->group(function () {
    // 文件管理
    Route::post('/file/upload', [FileController::class, 'upload']);     // 上传文件
    Route::post('/file/delete', [FileController::class, 'delete']);     // 删除文件
    Route::post('/file/cleanup', [FileController::class, 'cleanup']);   // 清空文件

    // 组织架构管理
    Route::post('/organization/index', [OrganizationController::class, 'index']);
    Route::post('/organization/add', [OrganizationController::class, 'add']);
    Route::post('/organization/update', [OrganizationController::class, 'update']);
    Route::post('/organization/delete', [OrganizationController::class, 'delete']);
    Route::post('/organization/move', [OrganizationController::class, 'move']);

    // 数据字典分类管理
    Route::post('/DictionaryCategory/index', [DictionaryCategoryController::class, 'index']);
    // 数据字典管理
    Route::post('/dictionary/index', [DictionaryController::class, 'index']);
    Route::post('/dictionary/all', [DictionaryController::class, 'all']);
    Route::post('/dictionary/add', [DictionaryController::class, 'add']);
    Route::post('/dictionary/update', [DictionaryController::class, 'update']);
    Route::post('/dictionary/delete', [DictionaryController::class, 'delete']);
    Route::post('/dictionary/batchDelete', [DictionaryController::class, 'batchDelete']);
    Route::get('/dictionary/getTplFile', [DictionaryController::class, 'getTplFile']);
    Route::post('/dictionary/getPidData', [DictionaryController::class, 'getPidData']);

    // 日志管理
    Route::post('/log/index', [LogController::class, 'index']);
    Route::post('/log/getSourceTypeList', [LogController::class, 'getSourceTypeList']);
    Route::post('/log/getActionList', [LogController::class, 'getActionList']);
});
