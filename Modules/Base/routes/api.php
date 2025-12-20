<?php

use Modules\Base\Http\Controllers\DictionaryCategoryController;
use Modules\Base\Http\Controllers\DictionaryController;
use Modules\Base\Http\Controllers\FileController;
use Modules\Base\Http\Controllers\LogController;
use Modules\Base\Http\Controllers\MenuController;
use Modules\Base\Http\Controllers\OrganizationController;
use Modules\Base\Http\Controllers\AccountController;
use Modules\Base\Http\Controllers\AppController;
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
    Route::post('/log/index', [LogController::class, 'index']);  // 常规日志列表
    Route::post('/log/operationLog', [LogController::class, 'operationLog']);  // 操作日志列表
    Route::post('/log/loginLog', [LogController::class, 'loginLog']);  // 登录日志列表
    Route::post('/log/auditLog', [LogController::class, 'auditLog']);  // 审计日志列表
    Route::post('/log/getSourceTypeList', [LogController::class, 'getSourceTypeList']);  // 来源类型列表
    Route::post('/log/getActionList', [LogController::class, 'getActionList']);  // 常规日志操作类型列表
    Route::post('/log/getOperationActionList', [LogController::class, 'getOperationActionList']);  // 操作日志操作类型列表
    Route::post('/log/getHttpMethodList', [LogController::class, 'getHttpMethodList']);  // HTTP方法列表
    Route::post('/log/getBrowserList', [LogController::class, 'getBrowserList']);  // 浏览器列表
    Route::post('/log/getOperatingSystemList', [LogController::class, 'getOperatingSystemList']);  // 操作系统列表
    Route::post('/log/getDeviceTypeList', [LogController::class, 'getDeviceTypeList']);  // 设备类型列表
    Route::post('/log/getAuditActionList', [LogController::class, 'getAuditActionList']);  // 审计操作类型列表
    Route::post('/log/getResourceTypeList', [LogController::class, 'getResourceTypeList']);  // 资源类型列表
    Route::post('/log/getModuleList', [LogController::class, 'getModuleList']);  // 操作日志模块名称列表
    Route::post('/log/getResponseCodeList', [LogController::class, 'getResponseCodeList']);  // 操作日志响应状态码列表
    Route::post('/log/getOperationLogSearchOptions', [LogController::class, 'getOperationLogSearchOptions']);  // 操作日志搜索框选项（整合接口）
    Route::post('/log/getIndexSearchData', [LogController::class, 'getIndexSearchData']);  // 常规日志搜索数据
    Route::post('/log/getOperationLogSearchData', [LogController::class, 'getOperationLogSearchData']);  // 操作日志搜索数据
    Route::post('/log/getLoginLogSearchData', [LogController::class, 'getLoginLogSearchData']);  // 登录日志搜索数据
    Route::post('/log/getAuditLogSearchData', [LogController::class, 'getAuditLogSearchData']);  // 审计日志搜索数据

    // 应用管理
    Route::post('/app/myApps', [AppController::class, 'getMyApps']);  // 获取我的应用列表
});
