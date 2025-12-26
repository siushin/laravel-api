<?php

namespace Modules\Base\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Base\Attributes\OperationAction;
use Modules\Base\Enums\OperationActionEnum;
use Modules\Base\Models\Department;

/**
 * 控制器：部门
 * @module 组织架构管理
 */
class DepartmentController extends Controller
{
    /**
     * 获取部门列表（全部）
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::list)]
    public function list(Request $request): JsonResponse
    {
        $params = trimParam($request->all());
        return success(Department::getAllData($params, ['department_id', 'department_code', 'department_name']));
    }
}
