<?php

use Illuminate\Support\Facades\Route;
use Modules\Sms\Http\Controllers\SmsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('sms', SmsController::class)->names('sms');
});
