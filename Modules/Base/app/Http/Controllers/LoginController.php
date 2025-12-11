<?php

namespace Modules\Base\Http\Controllers;

use Modules\Base\Models\Account;
use Modules\Base\Models\AccountSocial;
use Modules\Base\Enums\AccountTypeEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Siushin\LaravelTool\Enums\LogActionEnum;
use Siushin\LaravelTool\Enums\RequestSourceEnum;
use Siushin\LaravelTool\Enums\SocialTypeEnum;

/**
 * 控制器：用户登录/授权
 */
class LoginController extends Controller
{
    public int $expire_hour   = 2;
    public int $expire_minute = 120;
    public int $expire_second = 7200;

    public function __construct()
    {
        $this->expire_minute = config('sanctum.expiration') ?: $this->expire_minute;
        $this->expire_hour = $this->expire_minute / 60;
        $this->expire_second = $this->expire_minute * 60;
    }

    /**
     * 用户登录
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function login(Request $request): JsonResponse
    {
        // 验证请求数据（登录账号可以是用户名、邮箱或手机号）
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // AccessAuth 中间件已经根据路径注入了 request_source 和 account_type
        $requestSource = $request->get('request_source');
        $accountType = $request->get('account_type');

        // 验证请求来源
        $validSources = array_map(fn($case) => $case->value, RequestSourceEnum::cases());
        if (!in_array($requestSource, $validSources)) {
            throw_exception('请求来源有误');
        }

        // 尝试认证用户
        $extend_data = ['username' => $request['username']];

        // 先尝试通过用户名查找账号，并根据账号类型筛选
        $accountQuery = Account::query()->where('username', $request['username']);

        // 根据账号类型筛选（account_type 现在是字符串值）
        if ($accountType) {
            $accountQuery->where('account_type', $accountType);
        }

        $account = $accountQuery->first();

        // 如果通过用户名找不到，尝试通过邮箱或手机号在社交网络表中查找
        if (!$account) {
            $accountSocial = AccountSocial::query()
                ->whereIn('social_type', [
                    SocialTypeEnum::Email->value,
                    SocialTypeEnum::Mobile->value
                ])
                ->where('social_account', $request['username'])
                ->first();

            if ($accountSocial) {
                $accountQuery = Account::query()->where('id', $accountSocial->user_id);
                // 根据账号类型筛选
                if ($accountType) {
                    $accountQuery->where('account_type', $accountType);
                }
                $account = $accountQuery->first();
            }
        }

        if (!$account || !Hash::check($request['password'], $account->password)) {
            logging(LogActionEnum::fail_login->name, "尝试登录，登录失败(account: {$request['username']})", $extend_data);
            throw_exception('账号或密码不正确');
        }

        // 记录登录信息
        $account->update([
            'last_login_ip'   => $request->ip(),
            'last_login_time' => now(),
        ]);

        // 认证成功后生成并返回访问令牌
        $token = $account->createToken(
            'account_token', ['*'], now()->addHours($this->expire_hour)
        )->plainTextToken;

        // 获取用户数据（不包括token）
        $userData = $this->getUserData($account);

        $userData['token'] = self::buildTokenData($token, $this->expire_second);
        logging(LogActionEnum::login->name, "用户登录系统(account: {$request['username']})", $extend_data);
        return success($userData, '登录成功');
    }

    /**
     * 获取用户信息
     * @param Request $request
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    public function getUserInfo(Request $request): JsonResponse
    {
        $account = $request->user();
        $userData = $this->getUserData($account);
        return success($userData);
    }

    /**
     * 刷新 API 令牌
     * @param Request $request
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    public function refreshToken(Request $request): JsonResponse
    {
        // 将当前使用的token设为过期
        $currentToken = $request->user()->currentAccessToken();
        if ($currentToken) {
            $currentToken->update(['expires_at' => now()]);
        }

        // 创建新的token
        $token = $request->user()->createToken('account_token', ['*'], now()->addHours($this->expire_hour))->plainTextToken;
        return success(['token' => self::buildTokenData($token, $this->expire_second)]);
    }

    /**
     * 获取用户数据（不包括token）
     * @param Account $account
     * @return array
     * @author siushin<siushin@163.com>
     */
    private function getUserData(Account $account): array
    {
        // 根据账号类型加载对应的数据
        $userData = $account->toArray();

        // 加载对应的类型信息并合并到$userData
        if ($account->account_type === AccountTypeEnum::Admin) {
            $typeInfo = $account->adminInfo;
            if ($typeInfo) {
                // 只返回需要的字段：company_id, department_id，并合并到$userData
                $userData = array_merge($userData, $typeInfo->only(['company_id', 'department_id']));
            }
        } elseif ($account->account_type === AccountTypeEnum::User) {
            $typeInfo = $account->customerInfo;
            if ($typeInfo) {
                // User表只有id和user_id，没有其他业务字段，无需合并
            }
        }

        // 加载账号资料信息并合并到$userData
        $profile = $account->profile;
        if ($profile) {
            // 只返回需要的字段：real_name, gender, avatar，并合并到$userData
            $userData = array_merge($userData, $profile->only(['real_name', 'gender', 'avatar']));
        }

        // 加载所有已验证的社交信息
        $socialAccounts = $account->socialAccounts()
            ->where('is_verified', true)
            ->get()
            ->map(function ($social) {
                // 只返回需要的字段：social_type, social_account, social_name, avatar
                return $social->only(['social_type', 'social_account', 'social_name', 'avatar']);
            })
            ->toArray();
        $userData['social_accounts'] = $socialAccounts;

        return $userData;
    }

    /**
     * 构造 token 结构体
     * @param string $token
     * @param int    $expire_second
     * @return array
     * @author siushin<siushin@163.com>
     */
    private function buildTokenData(string $token, int $expire_second): array
    {
        return [
            'token_type'    => 'Bearer',
            'expires_in'    => $expire_second,
            'access_token'  => $token,
            'refresh_token' => $token,
        ];
    }
}
