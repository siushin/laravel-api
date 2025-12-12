<?php

namespace Modules\Sms\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * 控制器：短信服务
 */
class SmsController extends Controller
{
    /**
     * 验证码过期时间（分钟）
     */
    private int $codeExpireMinutes = 5;

    /**
     * IP 限流：时间窗口（秒）
     */
    private int $ipLimitWindow = 60;

    /**
     * IP 限流：时间窗口内最大请求次数
     */
    private int $ipLimitMaxRequests = 3;

    /**
     * 每天最大发送次数
     */
    private int $dailyMaxRequests = 10;

    /**
     * 发送短信验证码
     * @param Request $request
     * @return JsonResponse
     * @throws Exception|InvalidArgumentException
     * @author siushin<siushin@163.com>
     */
    public function sendSms(Request $request): JsonResponse
    {
        // 验证请求数据
        $request->validate([
            'mobile' => ['required', 'string', 'regex:/^1[3-9]\d{9}$/'],
        ], [
            'mobile.required' => '手机号不能为空',
            'mobile.regex'    => '手机号格式不正确',
        ]);

        $mobile = $request->input('mobile');
        $ip = $request->ip();

        // IP 频繁请求限制检查
        $this->checkIpLimit($ip);

        // 检查当天手机号请求总次数限制
        $this->checkDailyLimit($mobile);

        // 生成6位随机数字验证码
        $code = $this->generateCode();

        // 存储验证码到 Redis，设置过期时间
        $codeKey = "sms:code:$mobile";
        Cache::store('redis')->put($codeKey, $code, now()->addMinutes($this->codeExpireMinutes));

        // 记录 IP 请求次数
        $ipKey = "sms:ip:$ip";
        $requestCount = Cache::store('redis')->get($ipKey, 0);
        Cache::store('redis')->put($ipKey, $requestCount + 1, now()->addSeconds($this->ipLimitWindow));

        // 记录当天手机号请求次数
        $this->incrementDailyCount($mobile);

        // TODO: 实际项目中这里应该调用短信服务商 API 发送短信
        // 目前暂时返回验证码（仅用于开发测试）
        return success([
            'mobile' => $mobile,
            'code'   => $code, // 开发阶段返回验证码，生产环境应移除
            'expire' => $this->codeExpireMinutes,
        ], '验证码发送成功');
    }

    /**
     * 检查 IP 频繁请求限制
     * @param string $ip
     * @return void
     * @throws Exception|InvalidArgumentException
     * @author siushin<siushin@163.com>
     */
    private function checkIpLimit(string $ip): void
    {
        $ipKey = "sms:ip:$ip";
        $requestCount = Cache::store('redis')->get($ipKey, 0);

        if ($requestCount >= $this->ipLimitMaxRequests) {
            // 获取 Redis 中 key 的剩余过期时间（秒）
            // 需要获取 Cache 的完整 key（包含前缀）
            $cachePrefix = config('cache.prefix', '');
            $fullKey = $cachePrefix ? $cachePrefix . $ipKey : $ipKey;
            $ttl = Redis::connection('cache')->ttl($fullKey);

            if ($ttl > 0) {
                throw_exception("请求过于频繁，请 {$ttl} 秒后再试");
            } else {
                throw_exception("请求过于频繁，请稍后再试");
            }
        }
    }

    /**
     * 检查当天手机号请求总次数限制
     * @param string $mobile
     * @return void
     * @throws Exception|InvalidArgumentException
     * @author siushin<siushin@163.com>
     */
    private function checkDailyLimit(string $mobile): void
    {
        $date = now()->format('Y-m-d');
        $dailyKey = "sms:daily:$mobile:$date";
        // 注意：Laravel Cache 会自动添加前缀，实际 Redis key 格式为：{CACHE_PREFIX}sms:daily:{mobile}:{date}
        // 例如：laravel-api-cache-sms:daily:13800138000:2025-12-12
        $requestCount = Cache::store('redis')->get($dailyKey, 0);

        if ($requestCount >= $this->dailyMaxRequests) {
            throw_exception("今天发送次数已达上限（{$this->dailyMaxRequests}次），请明天再试");
        }
    }

    /**
     * 增加当天手机号请求次数
     * @param string $mobile
     * @return void
     * @throws InvalidArgumentException
     * @author siushin<siushin@163.com>
     */
    private function incrementDailyCount(string $mobile): void
    {
        $date = now()->format('Y-m-d');
        $dailyKey = "sms:daily:$mobile:$date";
        // 注意：Laravel Cache 会自动添加前缀，实际 Redis key 格式为：{CACHE_PREFIX}sms:daily:{mobile}:{date}
        // 例如：cache-sms:daily:13800138000:2025-12-12

        // 计算到明天0点的剩余秒数，确保 key 在当天结束时过期
        // 使用 tomorrow 的 00:00:00 时间戳减去当前时间戳
        $tomorrow = now()->addDay()->startOfDay();
        $secondsUntilMidnight = max(1, $tomorrow->timestamp - now()->timestamp);

        // 先检查 key 是否存在，如果不存在则创建
        $requestCount = Cache::store('redis')->get($dailyKey, 0);

        // 如果 key 不存在或值为 0，直接设置值为 1
        if ($requestCount == 0) {
            Cache::store('redis')->put($dailyKey, 1, now()->addSeconds($secondsUntilMidnight));
        } else {
            // 如果 key 已存在，增加计数并更新过期时间
            Cache::store('redis')->put($dailyKey, $requestCount + 1, now()->addSeconds($secondsUntilMidnight));
        }
    }

    /**
     * 获取完整的 Redis key（包含 Cache 前缀）
     * 用于在 Redis 客户端中查找数据
     * @param string $key
     * @return string
     * @author siushin<siushin@163.com>
     */
    private function getFullCacheKey(string $key): string
    {
        $cachePrefix = config('cache.prefix', '');
        return $cachePrefix ? $cachePrefix . $key : $key;
    }

    /**
     * 生成6位随机数字验证码
     * @return string
     * @author siushin<siushin@163.com>
     */
    private function generateCode(): string
    {
        return str_pad((string)mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
