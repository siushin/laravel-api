<?php

namespace Modules\Base\Http\Controllers;

use Modules\Base\Attributes\OperationAction;
use Modules\Base\Enums\OperationActionEnum;
use Modules\Base\Models\SysLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Base\Enums\LogActionEnum;
use Siushin\LaravelTool\Enums\RequestSourceEnum;

/**
 * 控制器：日志
 * @module 日志管理
 */
class LogController extends Controller
{
    /**
     * 日志列表（分页）
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::index)]
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
    #[OperationAction(OperationActionEnum::view)]
    public function getSourceTypeList(): JsonResponse
    {
        return success(enum_to_array(RequestSourceEnum::cases()));
    }

    /**
     * 日志操作类型列表
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::view)]
    public function getActionList(): JsonResponse
    {
        $all_action_list = enum_to_array(LogActionEnum::cases(), 'array');
        return success($all_action_list);
    }
}
