<?php

/**
 * 助手函数：日志、调试（基于Laravel）
 */

use Modules\Base\Models\AccountSocial;
use Modules\Base\Models\SysLog;
use Modules\Base\Services\LogService;
use Illuminate\Support\Facades\Log;
use Siushin\LaravelTool\Enums\RequestSourceEnum;
use Siushin\LaravelTool\Enums\SocialTypeEnum;

/**
 * 写入日志（通用日志）
 * @param string $action_type
 * @param string $content
 * @param array  $extend_data
 * @return bool
 * @author siushin<siushin@163.com>
 */
function logging(string $action_type, string $content, array $extend_data = []): bool
{
    try {
        $logService = app(LogService::class);
        return $logService->logGeneral($action_type, $content, $extend_data);
    } catch (Exception $e) {
        Log::error('记录通用日志失败: ' . $e->getMessage());
        return false;
    }
}
