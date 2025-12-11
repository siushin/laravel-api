<?php

namespace Modules\Base\Http\Controllers;

use Modules\Base\Models\Account;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * 控制器：账号
 */
class AccountController extends Controller
{
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
