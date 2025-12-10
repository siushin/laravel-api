<?php

namespace App\Http\Controllers;

use App\Models\SysLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Siushin\LaravelTool\Enums\LogActionEnum;
use Siushin\LaravelTool\Enums\RequestSourceEnum;

class LogController extends Controller
{
    /**
     * 日志列表（分页）
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function index(Request $request): JsonResponse
    {
        $params = $request->all();
        return success(SysLog::getPageData($params));
    }

    /**
     * 来源类型列表
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    public function getSourceTypeList(): JsonResponse
    {
        return success(enum_to_array(RequestSourceEnum::cases()));
    }

    /**
     * 日志操作类型列表
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    public function getActionList(): JsonResponse
    {
        $all_action_list = enum_to_array(LogActionEnum::cases(), 'array');
        return success($all_action_list);
    }
}
