<?php

namespace Modules\Base\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Base\Attributes\OperationAction;
use Modules\Base\Enums\OperationActionEnum;
use Modules\Base\Models\Company;

/**
 * 控制器：公司
 * @module 组织架构管理
 */
class CompanyController extends Controller
{
    /**
     * 获取公司列表（全部）
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::list)]
    public function list(Request $request): JsonResponse
    {
        $params = trimParam($request->all());
        return success(Company::getAllData($params, ['company_id', 'company_code', 'company_name']));
    }
}
