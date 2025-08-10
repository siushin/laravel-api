<?php

use App\Http\Controllers\DemoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 测试接口
Route::post('/demo', [DemoController::class, 'index']);
