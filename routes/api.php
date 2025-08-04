<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 公共路由
Route::post('/admin/login', [LoginController::class, 'index']);

// API鉴权 路由组
Route::middleware(['auth:sanctum'])->prefix('/admin')->group(function () {
    // 管理员鉴权信息
    Route::post('/info', fn(Request $request) => $request->user());
    Route::post('/refreshToken', [LoginController::class, 'refreshToken']);
    Route::post('/changePassword', [UserController::class, 'changePassword']);

    // 用户管理

    // 日志管理
});
