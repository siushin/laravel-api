<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 控制器：用户
 */
class UserController extends Controller
{
    /**
     * 修改用户密码
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function changePassword(Request $request): JsonResponse
    {
        $params = $request->all();
        $params['user_id'] = auth()->id();  // 获取当前登录用户ID
        return success(User::updatePassword($params));
    }
}
