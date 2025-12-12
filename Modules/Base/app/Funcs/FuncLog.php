<?php

/**
 * 助手函数：日志、调试（基于Laravel）
 */

use Modules\Base\Models\AccountSocial;
use Modules\Base\Models\SysLog;
use Illuminate\Support\Facades\Log;
use Siushin\LaravelTool\Enums\RequestSourceEnum;
use Siushin\LaravelTool\Enums\SocialTypeEnum;

/**
 * 写入日志
 * @param string $action_type
 * @param string $content
 * @param array  $extend_data
 * @return bool
 * @author siushin<siushin@163.com>
 */
function logging(string $action_type, string $content, array $extend_data = []): bool
{
    try {
        $account_id = currentUserId() ?? null;

        // 如果 account_id 为空，且 extend_data 中包含 mobile，尝试通过手机号查找 user_id
        if (!$account_id) {
            $mobile = null;

            // 尝试从 extend_data 中提取手机号（支持多种格式）
            if (!empty($extend_data['mobile'])) {
                $mobile = $extend_data['mobile'];
            } elseif (!empty($extend_data['request']['mobile'])) {
                $mobile = $extend_data['request']['mobile'];
            }

            if ($mobile) {
                $accountSocial = AccountSocial::query()
                    ->where('social_type', SocialTypeEnum::Mobile->value)
                    ->where('social_account', $mobile)
                    ->first();

                if ($accountSocial) {
                    $account_id = $accountSocial->user_id;
                }
            }
        }

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
        $id = generateId();
        $data = compact('id', 'account_id', 'source_type', 'action_type', 'content', 'ip_address', 'ip_location', 'extend_data', 'created_at');
        return SysLog::query()->insert($data);
    } catch (Exception $e) {
        Log::error($e->getMessage());
        return false;
    }
}
