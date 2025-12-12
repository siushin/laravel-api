<?php

namespace Modules\Sms\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Sms\Enums\SmsTypeEnum;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * 短信服务类
 */
class SmsService
{
    /**
     * 短信类型配置
     * 每种类型可以设置不同的限制规则
     */
    private array $typeConfig = [
        // 注册验证码
        SmsTypeEnum::Register->value      => [
            'code_expire_minutes'   => 5,       // 验证码过期时间（分钟）
            'ip_limit_window'       => 60,      // IP 限流：时间窗口（秒）
            'ip_limit_max_requests' => 3,       // IP 限流：时间窗口内最大请求次数
            'daily_max_requests'    => 10,      // 每天最大发送次数
        ],
        // 登录验证码
        SmsTypeEnum::Login->value         => [
            'code_expire_minutes'   => 5,
            'ip_limit_window'       => 60,
            'ip_limit_max_requests' => 5,
            'daily_max_requests'    => 20,
        ],
        // 重置密码验证码
        SmsTypeEnum::ResetPassword->value => [
            'code_expire_minutes'   => 10,
            'ip_limit_window'       => 60,
            'ip_limit_max_requests' => 3,
            'daily_max_requests'    => 5,
        ],
        // 绑定手机号验证码
        SmsTypeEnum::BindMobile->value    => [
            'code_expire_minutes'   => 5,
            'ip_limit_window'       => 60,
            'ip_limit_max_requests' => 3,
            'daily_max_requests'    => 10,
        ],
        // 更换手机号验证码
        SmsTypeEnum::ChangeMobile->value  => [
            'code_expire_minutes'   => 5,
            'ip_limit_window'       => 60,
            'ip_limit_max_requests' => 3,
            'daily_max_requests'    => 5,
        ],
    ];

    /**
     * 校验参数
     * @param array $data 请求数据数组
     * @return array ['mobile' => string, 'type' => SmsTypeEnum]
     * @throws ValidationException
     * @author siushin<siushin@163.com>
     */
    public function validateParams(array $data): array
    {
        $validator = Validator::make($data, [
            'mobile' => ['required', 'string', 'regex:/^1[3-9]\d{9}$/'],
            'type'   => ['required', 'string', 'in:register,login,reset_password,bind_mobile,change_mobile'],
        ], [
            'mobile.required' => '手机号不能为空',
            'mobile.regex'    => '手机号格式不正确',
            'type.required'   => '短信类型不能为空',
            'type.in'         => '短信类型不正确',
        ]);

        $validated = $validator->validate();

        return [
            'mobile' => $validated['mobile'],
            'type'   => SmsTypeEnum::from($validated['type']),
        ];
    }

    /**
     * 发送短信验证码（便捷方法）
     * @param string $mobile 手机号
     * @param string $type   短信类型（register/login/reset_password/bind_mobile/change_mobile）
     * @return array
     * @throws Exception|InvalidArgumentException
     * @author siushin<siushin@163.com>
     */
    public function sendSmsCode(string $mobile, string $type): array
    {
        return $this->sendVerificationCode($mobile, SmsTypeEnum::from($type));
    }

    /**
     * 发送短信验证码
     * @param string      $mobile 手机号
     * @param SmsTypeEnum $type   短信类型
     * @return array
     * @throws Exception|InvalidArgumentException
     * @author siushin<siushin@163.com>
     */
    public function sendVerificationCode(string $mobile, SmsTypeEnum $type): array
    {
        $typeValue = $type->value;
        $config = $this->getTypeConfig($typeValue);

        // 获取 IP 地址
        $ip = request()->ip();

        // IP 频繁请求限制检查
        $this->checkIpLimit($ip, $typeValue, $config);

        // 检查当天手机号请求总次数限制
        $this->checkDailyLimit($mobile, $typeValue, $config);

        // 生成6位随机数字验证码
        $code = $this->generateCode();

        // 存储验证码到 Redis，设置过期时间
        $codeKey = $this->getCodeKey($mobile, $typeValue);
        Cache::store('redis')->put($codeKey, $code, now()->addMinutes($config['code_expire_minutes']));

        // 记录 IP 请求次数
        $this->incrementIpCount($ip, $typeValue, $config);

        // 记录当天手机号请求次数
        $this->incrementDailyCount($mobile, $typeValue, $config);

        // TODO: 实际项目中这里应该调用短信服务商 API 发送短信
        // 目前暂时返回验证码（仅用于开发测试）
        return [
            'mobile' => $mobile,
            'type'   => $typeValue,
            'code'   => $code, // 开发阶段返回验证码，生产环境应移除
            'expire' => $config['code_expire_minutes'],
        ];
    }

    /**
     * 验证验证码
     * @param string      $mobile 手机号
     * @param string      $code   验证码
     * @param SmsTypeEnum $type   短信类型
     * @return bool
     * @throws InvalidArgumentException
     * @author siushin<siushin@163.com>
     */
    public function verifyCode(string $mobile, string $code, SmsTypeEnum $type): bool
    {
        $codeKey = $this->getCodeKey($mobile, $type->value);
        $cachedCode = Cache::store('redis')->get($codeKey);

        if ($cachedCode === null || $cachedCode !== $code) {
            return false;
        }

        // 验证成功后删除验证码
        Cache::store('redis')->forget($codeKey);

        return true;
    }

    /**
     * 检查 IP 频繁请求限制
     * @param string $ip     IP地址
     * @param string $type   短信类型
     * @param array  $config 配置
     * @return void
     * @throws Exception|InvalidArgumentException
     * @author siushin<siushin@163.com>
     */
    private function checkIpLimit(string $ip, string $type, array $config): void
    {
        $ipKey = $this->getIpKey($ip, $type);
        $requestCount = Cache::store('redis')->get($ipKey, 0);

        if ($requestCount >= $config['ip_limit_max_requests']) {
            // 获取 Redis 中 key 的剩余过期时间（秒）
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
     * @param string $mobile 手机号
     * @param string $type   短信类型
     * @param array  $config 配置
     * @return void
     * @throws Exception|InvalidArgumentException
     * @author siushin<siushin@163.com>
     */
    private function checkDailyLimit(string $mobile, string $type, array $config): void
    {
        $dailyKey = $this->getDailyKey($mobile, $type);
        $requestCount = Cache::store('redis')->get($dailyKey, 0);

        if ($requestCount >= $config['daily_max_requests']) {
            throw_exception("今天发送次数已达上限（{$config['daily_max_requests']}次），请明天再试");
        }
    }

    /**
     * 增加 IP 请求次数
     * @param string $ip     IP地址
     * @param string $type   短信类型
     * @param array  $config 配置
     * @return void
     * @throws InvalidArgumentException
     * @author siushin<siushin@163.com>
     */
    private function incrementIpCount(string $ip, string $type, array $config): void
    {
        $ipKey = $this->getIpKey($ip, $type);
        $requestCount = Cache::store('redis')->get($ipKey, 0);
        Cache::store('redis')->put($ipKey, $requestCount + 1, now()->addSeconds($config['ip_limit_window']));
    }

    /**
     * 增加当天手机号请求次数
     * @param string $mobile 手机号
     * @param string $type   短信类型
     * @param array  $config 配置
     * @return void
     * @throws InvalidArgumentException
     * @author siushin<siushin@163.com>
     */
    private function incrementDailyCount(string $mobile, string $type, array $config): void
    {
        $dailyKey = $this->getDailyKey($mobile, $type);

        // 计算到明天0点的剩余秒数，确保 key 在当天结束时过期
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
     * 获取验证码 Redis key
     * @param string $mobile 手机号
     * @param string $type   短信类型
     * @return string
     * @author siushin<siushin@163.com>
     */
    private function getCodeKey(string $mobile, string $type): string
    {
        return "sms:code:{$type}:{$mobile}";
    }

    /**
     * 获取 IP 限流 Redis key
     * @param string $ip   IP地址
     * @param string $type 短信类型
     * @return string
     * @author siushin<siushin@163.com>
     */
    private function getIpKey(string $ip, string $type): string
    {
        return "sms:ip:{$type}:{$ip}";
    }

    /**
     * 获取每日限流 Redis key
     * @param string $mobile 手机号
     * @param string $type   短信类型
     * @return string
     * @author siushin<siushin@163.com>
     */
    private function getDailyKey(string $mobile, string $type): string
    {
        $date = now()->format('Y-m-d');
        return "sms:daily:{$type}:{$mobile}:{$date}";
    }

    /**
     * 获取短信类型配置
     * @param string $type 短信类型
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    private function getTypeConfig(string $type): array
    {
        if (!isset($this->typeConfig[$type])) {
            throw_exception("不支持的短信类型：{$type}");
        }

        return $this->typeConfig[$type];
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

