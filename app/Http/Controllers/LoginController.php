<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Siushin\LaravelTool\Enums\SysLogAction;
use Siushin\LaravelTool\Enums\SysUserType;

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
     * 登录
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author lintong<siushin@163.com>
     */
    public function index(Request $request): JsonResponse
    {
        // 验证请求数据
        $request->validate([
            'username' => ['required', Rule::exists('users')],
            'password' => ['required'],
        ]);

        // 根据 url_path 的值，从/api后开始切割最近一个path，判断用户类型
        $url_path_list = array_filter(explode('/', substr($request->path(), 4)));
        $request['user_type'] = strtolower($url_path_list[0] ?? 'admin');
        $request['user_type'] !== SysUserType::{$request['user_type']}->name && throw_exception('用户类型有误');

        // 尝试认证用户
        $extend_data = ['username' => $request['username']];
        $user = User::query()->where('username', $request['username'])->orWhere('email', $request['username'])->first();
        if (!$user || !Hash::check($request['password'], $user->password)) {
            logging(SysLogAction::fail_login->name, "尝试登录，登录失败(user: {$request['username']})", $extend_data);
            throw_exception('账号或密码不正确');
        }

        // 认证成功后生成并返回访问令牌
        $token = $user->createToken(
            'user_token', ['*'], now()->addHours($this->expire_hour)
        )->plainTextToken;

        $data = [
            'code' => 0,
            'message' => '登录成功',
            'data' => $user,
            'token' => self::buildTokenData($token, $this->expire_second)
        ];
        logging(SysLogAction::login->name, "用户登录系统(user: {$request['username']})", $extend_data);
        return response()->json($data);
    }

    /**
     * 刷新 API 令牌
     * @param Request $request
     * @return JsonResponse
     * @author lintong<siushin@163.com>
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
     * @author lintong<siushin@163.com>
     */
    private function buildTokenData(string $token, int $expire_second): array
    {
        return [
            'token_type' => 'Bearer',
            'expires_in' => $expire_second,
            'access_token' => $token,
            'refresh_token' => $token,
        ];
    }
}
