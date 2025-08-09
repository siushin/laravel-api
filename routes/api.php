<?php

use App\Http\Controllers\DemoController;
use App\Http\Controllers\DictionaryCategoryController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 测试接口
Route::post('/demo', [DemoController::class, 'index']);

// 公共路由
Route::post('/admin/login', [LoginController::class, 'index']);
// 下载数据字典模板
Route::get('/dictionary/getTplFile', [DictionaryController::class, 'getTplFile']);

// API鉴权 路由组
Route::prefix('/admin')->middleware(['auth:sanctum'])->group(function () {
    // 管理员鉴权信息
    Route::post('/info', fn(Request $request) => $request->user());
    Route::post('/refreshToken', [LoginController::class, 'refreshToken']);
    Route::post('/changePassword', [UserController::class, 'changePassword']);

    // 文件管理
    Route::post('/file/upload', [FileController::class, 'upload']); // 上传文件

    // TODO 用户管理

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
