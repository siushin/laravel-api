<?php

namespace Modules\Base\Http\Controllers;

use Modules\Base\Models\Account;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $params = $request->all();
        $params['user_id'] = auth()->id();  // 获取当前登录账号ID
        return success(Account::updatePassword($params));
    }
}
