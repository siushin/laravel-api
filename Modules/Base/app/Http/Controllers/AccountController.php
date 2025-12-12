<?php

namespace Modules\Base\Http\Controllers;

use Modules\Base\Enums\AccountTypeEnum;
use Modules\Base\Models\Account;
use Modules\Base\Models\AccountProfile;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Base\Models\AccountSocial;
use Modules\Base\Services\AuthService;
use Modules\Sms\Enums\SmsTypeEnum;
use Modules\Sms\Services\SmsService;
use Psr\SimpleCache\InvalidArgumentException;
use Modules\Base\Enums\LogActionEnum;
use Siushin\LaravelTool\Enums\RequestSourceEnum;
use Siushin\LaravelTool\Enums\SocialTypeEnum;

/**
 * 控制器：账号
 */
class AccountController extends Controller
{
    /**
     * 认证服务
     */
    private AuthService $authService;

    /**
     * 短信服务
     */
    private SmsService $smsService;

    public function __construct(AuthService $authService, SmsService $smsService)
    {
        $this->authService = $authService;
        $this->smsService = $smsService;
    }

    /**
     * 用户登录（用户名+密码）
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

        // 验证请求来源和账号类型
        $sourceInfo = $this->authService->validateRequestSource($request);
        $accountType = $sourceInfo['account_type'];

        // 尝试认证用户
        $extend_data = ['username' => $request['username']];

        // 通过用户名、邮箱或手机号查找账号
        $account = $this->authService->findAccountByIdentifier($request['username'], $accountType);

        // 验证账号和密码
        if (!$account || !$this->authService->verifyPassword($account, $request['password'])) {
            logging(LogActionEnum::fail_login->name, "尝试登录，登录失败(account: {$request['username']})", $extend_data);
            throw_exception('账号或密码不正确');
        }

        // 执行登录流程
        $result = $this->authService->processLogin($account, $request, $request['username'], $extend_data);

        return success($result['user_data'], '登录成功');
    }

    /**
     * 用户登录（手机号+验证码）
     * @param Request $request
     * @return JsonResponse
     * @throws Exception|InvalidArgumentException
     * @author siushin<siushin@163.com>
     */
    public function loginByCode(Request $request): JsonResponse
    {
        // 验证请求数据
        $request->validate([
            'mobile' => ['required', 'string', 'regex:/^1[3-9]\d{9}$/'],
            'code'   => ['required', 'string', 'size:6'],
        ], [
            'mobile.required' => '手机号不能为空',
            'mobile.regex'    => '手机号格式不正确',
            'code.required'   => '验证码不能为空',
            'code.size'       => '验证码必须为6位数字',
        ]);

        // 验证请求来源和账号类型
        $sourceInfo = $this->authService->validateRequestSource($request);
        $accountType = $sourceInfo['account_type'];

        $mobile = $request['mobile'];
        $code = $request['code'];

        // 验证短信验证码
        if (!$this->smsService->verifyCode($mobile, $code, SmsTypeEnum::Login)) {
            $extend_data = [
                'mobile' => $mobile,
                'code' => $code,
            ];
            logging(LogActionEnum::fail_login->name, "尝试登录，验证码错误(mobile: {$mobile})", $extend_data);
            throw_exception('验证码错误或已过期');
        }

        // 通过手机号查找账号
        $account = $this->authService->findAccountByMobile($mobile, $accountType);

        if (!$account) {
            $extend_data = [
                'mobile' => $mobile,
                'code' => $code,
            ];
            logging(LogActionEnum::fail_login->name, "尝试登录，账号不存在(mobile: {$mobile})", $extend_data);
            throw_exception('该手机号未注册');
        }

        // 执行登录流程
        $extend_data = [
            'mobile' => $mobile,
            'code' => $code,
        ];
        $result = $this->authService->processLogin($account, $request, $mobile, $extend_data);

        return success($result['user_data'], '登录成功');
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
        $userData = $this->authService->getUserData($account);
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
        $account = $request->user();

        // 将当前使用的token设为过期
        $currentToken = $account->currentAccessToken();
        if ($currentToken) {
            $currentToken->update(['expires_at' => now()]);
        }

        // 创建新的token
        $token = $this->authService->generateToken($account);
        return success(['token' => $this->authService->buildTokenData($token)]);
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
            'username'         => ['required', 'string', 'max:255'],
            'password'         => ['required', 'string', 'min:6'],
            'confirm_password' => ['required', 'string', 'same:password'],
            'mobile'           => ['required', 'string', 'regex:/^1[3-9]\d{9}$/'],
            'code'             => ['required', 'string', 'size:6'],
        ], [
            'username.required'         => '用户名不能为空',
            'password.required'         => '密码不能为空',
            'password.min'              => '密码长度至少6位',
            'confirm_password.required' => '确认密码不能为空',
            'confirm_password.same'     => '两次输入的密码不一致',
            'mobile.required'           => '手机号不能为空',
            'mobile.regex'              => '手机号格式不正确',
            'code.required'             => '验证码不能为空',
            'code.size'                 => '验证码必须为6位数字',
        ]);

        // AccessAuth 中间件已经根据路径注入了 request_source 和 account_type
        $requestSource = $request->get('request_source');
        $accountType = $request->get('account_type');

        // 验证请求来源
        $validSources = array_map(fn($case) => $case->value, RequestSourceEnum::cases());
        if (!in_array($requestSource, $validSources)) {
            throw_exception('请求来源有误');
        }

        // 验证注册验证码
        if (!$this->smsService->verifyCode($request['mobile'], $request['code'], SmsTypeEnum::Register)) {
            throw_exception('验证码错误或已过期');
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

        // 创建账号资料记录
        AccountProfile::create([
            'id'       => generateId(),
            'user_id'  => $account->id,
            'nickname' => $request['username'],
        ]);

        // 创建手机号社交账号记录
        AccountSocial::create([
            'user_id'        => $account->id,
            'social_type'    => SocialTypeEnum::Mobile->value,
            'social_account' => $request['mobile'],
            'is_verified'    => false,
        ]);

        // 记录注册日志
        $extend_data = [
            'username' => $request['username'],
            'mobile' => $request['mobile'],
            'code' => $request['code'],
        ];
        logging(LogActionEnum::login->name, "用户注册成功(account: {$request['username']})", $extend_data);

        return success([], '注册成功');
    }

    /**
     * 重置密码
     * @param Request $request
     * @return JsonResponse
     * @throws Exception|InvalidArgumentException
     * @author siushin<siushin@163.com>
     */
    public function resetPassword(Request $request): JsonResponse
    {
        // 验证请求数据
        $request->validate([
            'mobile'           => ['required', 'string', 'regex:/^1[3-9]\d{9}$/'],
            'code'             => ['required', 'string', 'size:6'],
            'password'         => ['required', 'string', 'min:6'],
            'confirm_password' => ['required', 'string', 'same:password'],
        ], [
            'mobile.required'           => '手机号不能为空',
            'mobile.regex'              => '手机号格式不正确',
            'code.required'             => '验证码不能为空',
            'code.size'                 => '验证码必须为6位数字',
            'password.required'         => '新密码不能为空',
            'password.string'           => '新密码必须是字符串',
            'password.min'              => '密码长度至少6位',
            'confirm_password.required' => '确认新密码不能为空',
            'confirm_password.string'   => '确认新密码必须是字符串',
            'confirm_password.same'     => '两次输入的密码不一致',
        ]);

        $mobile = $request['mobile'];
        $code = $request['code'];

        // 验证重置密码验证码
        if (!$this->smsService->verifyCode($mobile, $code, SmsTypeEnum::ResetPassword)) {
            $extend_data = [
                'mobile' => $mobile,
                'code' => $code,
            ];
            logging(LogActionEnum::reset_password->name, "尝试重置密码，验证码错误(mobile: {$mobile})", $extend_data);
            throw_exception('验证码错误或已过期');
        }

        // 通过手机号查找账号（不限制账号类型）
        $account = $this->authService->findAccountByMobile($mobile, null);

        if (!$account) {
            $extend_data = [
                'mobile' => $mobile,
                'code' => $code,
            ];
            logging(LogActionEnum::reset_password->name, "尝试重置密码，账号不存在(mobile: {$mobile})", $extend_data);
            throw_exception('该手机号未注册');
        }

        // 更新密码
        $params = [
            'user_id'  => $account->id,
            'password' => $request->input('password'),
        ];

        Account::updatePassword($params);

        // 记录重置密码日志（不包含密码）
        $extend_data = [
            'mobile' => $mobile,
            'code' => $code,
            'username' => $account->username,
        ];
        logging(LogActionEnum::reset_password->name, "用户重置密码成功(mobile: {$mobile})", $extend_data);

        return success([], '密码重置成功');
    }

    /**
     * 修改账号密码
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function changePassword(Request $request): JsonResponse
    {
        // 验证请求参数
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:6'],
            'confirm_password' => ['required', 'string', 'same:password'],
        ], [
            'current_password.required' => '当前密码不能为空',
            'current_password.string'   => '当前密码必须是字符串',
            'password.required'         => '新密码不能为空',
            'password.string'           => '新密码必须是字符串',
            'password.min'              => '新密码至少为 :min 个字符',
            'confirm_password.required' => '确认密码不能为空',
            'confirm_password.string'   => '确认密码必须是字符串',
            'confirm_password.same'     => '确认密码与新密码不一致',
        ]);

        $account = $request->user();

        // 验证当前密码是否正确
        if (!Hash::check($request->input('current_password'), $account->password)) {
            throw_exception('当前密码不正确');
        }

        // 更新密码
        $params = [
            'user_id'  => $account->id,
            'password' => $request->input('password'),
        ];

        return success(Account::updatePassword($params), '密码修改成功');
    }
}
