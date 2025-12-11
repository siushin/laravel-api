<?php

use Modules\Base\Http\Controllers\DictionaryCategoryController;
use Modules\Base\Http\Controllers\DictionaryController;
use Modules\Base\Http\Controllers\FileController;
use Modules\Base\Http\Controllers\LogController;
use Modules\Base\Http\Controllers\LoginController;
use Modules\Base\Http\Controllers\OrganizationController;
use Modules\Base\Http\Controllers\AccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 公共路由
Route::get('/dictionary/getTplFile', [DictionaryController::class, 'getTplFile']);  // 下载数据字典模板

// 不需要认证的接口
Route::post('/user/login', [LoginController::class, 'login']);
Route::post('/admin/login', [LoginController::class, 'login']);

// API鉴权 用户 路由组
Route::prefix('/user')->middleware(['auth:sanctum'])->group(function () {
    // 用户 鉴权信息
    Route::post('/info', [LoginController::class, 'getUserInfo']);
    Route::post('/refreshToken', [LoginController::class, 'refreshToken']);
    Route::post('/changePassword', [AccountController::class, 'changePassword']);
    Route::post('/logout', [LoginController::class, 'logout']);
});

// API鉴权 管理员 路由组
Route::prefix('/admin')->middleware(['auth:sanctum'])->group(function () {
    // 管理员 鉴权信息
    Route::post('/info', [LoginController::class, 'getUserInfo']);
    Route::post('/refreshToken', [LoginController::class, 'refreshToken']);
    Route::post('/changePassword', [AccountController::class, 'changePassword']);
    Route::post('/logout', [LoginController::class, 'logout']);

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
