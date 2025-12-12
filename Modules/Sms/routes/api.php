<?php

use Illuminate\Support\Facades\Route;
use Modules\Sms\Http\Controllers\SmsController;

// 不需要认证的接口
Route::post('/sms/send', [SmsController::class, 'sendSms']);  // 发送短信验证码
