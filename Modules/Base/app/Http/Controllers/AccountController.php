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
