<?php

namespace Modules\Base\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Base\Enums\AccountTypeEnum;
use Modules\Base\Models\Account;
use Modules\Base\Models\AccountSocial;
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
     * 退出用户登录
     * @param Request $request
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    public function logout(Request $request): JsonResponse
    {
        $account = $request->user();

        // 获取当前使用的token并删除
        $currentToken = $account->currentAccessToken();
        if ($currentToken) {
            // 删除当前token
            $currentToken->delete();
        }

        // 记录用户退出登录日志
        $extend_data = ['username' => $account->username];
        logging('logout', "用户退出登录(account: {$account->username})", $extend_data);

        return success([], '用户退出登录成功');
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

    /**
     * 用户注册
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function register(Request $request): JsonResponse
    {
        // 验证请求数据
        $request->validate([
            'username'              => ['required', 'string', 'max:255'],
            'password'              => ['required', 'string', 'min:6'],
            'password_confirmation' => ['required', 'string', 'same:password'],
            'mobile'                => ['required', 'string', 'regex:/^1[3-9]\d{9}$/'],
        ], [
            'username.required'              => '用户名不能为空',
            'password.required'              => '密码不能为空',
            'password.min'                   => '密码长度至少6位',
            'password_confirmation.required' => '确认密码不能为空',
            'password_confirmation.same'     => '两次输入的密码不一致',
            'mobile.required'                => '手机号不能为空',
            'mobile.regex'                   => '手机号格式不正确',
        ]);

        // AccessAuth 中间件已经根据路径注入了 request_source 和 account_type
        $requestSource = $request->get('request_source');
        $accountType = $request->get('account_type');

        // 验证请求来源
        $validSources = array_map(fn($case) => $case->value, RequestSourceEnum::cases());
        if (!in_array($requestSource, $validSources)) {
            throw_exception('请求来源有误');
        }

        // 检查用户名是否已存在
        $existingAccount = Account::query()
            ->where('username', $request['username'])
            ->first();

        if ($existingAccount) {
            throw_exception('用户名已存在');
        }

        // 检查手机号是否已存在
        $existingMobile = AccountSocial::query()
            ->where('social_type', SocialTypeEnum::Mobile->value)
            ->where('social_account', $request['mobile'])
            ->first();

        if ($existingMobile) {
            throw_exception('手机号已被注册');
        }

        // 创建账号
        $account = Account::create([
            'username'     => $request['username'],
            'password'     => Hash::make($request['password']),
            'account_type' => $accountType ?: AccountTypeEnum::User->value,
            'status'       => 1,
        ]);

        // 创建手机号社交账号记录
        AccountSocial::create([
            'user_id'        => $account->id,
            'social_type'    => SocialTypeEnum::Mobile->value,
            'social_account' => $request['mobile'],
            'is_verified'    => false,
        ]);

        // 记录注册日志
        $extend_data = ['username' => $request['username'], 'mobile' => $request['mobile']];
        logging(LogActionEnum::login->name, "用户注册成功(account: {$request['username']})", $extend_data);

        return success([], '注册成功');
    }
}
