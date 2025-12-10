<?php

/**
 * 助手函数：日志、调试（基于Laravel）
 */

use App\Models\SysLog;
use Illuminate\Support\Facades\Log;
use Siushin\LaravelTool\Enums\RequestSourceEnum;

/**
 * 写入日志
 * @param string     $action_type
 * @param string     $content
 * @param array|null $extend_data
 * @return bool
 * @author siushin<siushin@163.com>
 */
function logging(string $action_type, string $content, array $extend_data = null): bool
{
    try {
        $account_id = currentUserId() ?? null;
        $source_type = request()->request_source ?? RequestSourceEnum::guest->value;
        $ip_address = request()->ip();

        $ip2region = new Ip2Region();
        try {
            $ip_location = $ip2region->simple($ip_address);
        } catch (Exception $e) {
            $ip_location = '';
        }

        $created_at = getDateTimeArr()['datetime'];
        $extend_data && $extend_data = json_encode($extend_data, JSON_UNESCAPED_UNICODE);
        $data = compact('account_id', 'source_type', 'action_type', 'content', 'ip_address', 'ip_location', 'extend_data', 'created_at');
        return SysLog::query()->insert($data);
    } catch (Exception $e) {
        Log::error($e->getMessage());
        return false;
    }
}
