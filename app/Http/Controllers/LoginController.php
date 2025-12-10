<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSocial;
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

        // 验证访问来源标识
        $validSources = array_map(fn($case) => $case->value, RequestSourceEnum::cases());
        if (!in_array($requestSource, $validSources)) {
            throw_exception('访问来源标识有误');
        }

        // 尝试认证用户
        $extend_data = ['username' => $request['username']];

        // 先尝试通过用户名查找用户，并根据账号类型筛选
        $userQuery = User::query()->where('username', $request['username']);

        // 根据账号类型筛选（account_type 现在是字符串值）
        if ($accountType) {
            $userQuery->where('account_type', $accountType);
        }

        $user = $userQuery->first();

        // 如果通过用户名找不到，尝试通过邮箱或手机号在社交网络表中查找
        if (!$user) {
            $userSocial = UserSocial::query()
                ->whereIn('social_type', [
                    SocialTypeEnum::Email->value,
                    SocialTypeEnum::Mobile->value
                ])
                ->where('social_account', $request['username'])
                ->first();

            if ($userSocial) {
                $userQuery = User::query()->where('id', $userSocial->user_id);
                // 根据账号类型筛选
                if ($accountType) {
                    $userQuery->where('account_type', $accountType);
                }
                $user = $userQuery->first();
            }
        }

        if (!$user || !Hash::check($request['password'], $user->password)) {
            logging(LogActionEnum::fail_login->name, "尝试登录，登录失败(user: {$request['username']})", $extend_data);
            throw_exception('账号或密码不正确');
        }

        // 记录登录信息
        $user->update([
            'last_login_ip'   => $request->ip(),
            'last_login_time' => now(),
        ]);

        // 认证成功后生成并返回访问令牌
        $token = $user->createToken(
            'user_token', ['*'], now()->addHours($this->expire_hour)
        )->plainTextToken;

        $data = [
            'code'    => 0,
            'message' => '登录成功',
            'data'    => $user,
            'token'   => self::buildTokenData($token, $this->expire_second)
        ];
        logging(LogActionEnum::login->name, "用户登录系统(user: {$request['username']})", $extend_data);
        return response()->json($data);
    }

    /**
     * 刷新 API 令牌
     * @param Request $request
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $token = $request->user()->createToken('user_token', ['*'], now()->addHours($this->expire_hour))->plainTextToken;
        return success(['token' => self::buildTokenData($token, $this->expire_second)]);
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
