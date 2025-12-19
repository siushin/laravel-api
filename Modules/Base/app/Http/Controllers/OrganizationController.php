<?php

namespace Modules\Base\Http\Controllers;

use Modules\Base\Attributes\OperationAction;
use Modules\Base\Enums\OperationActionEnum;
use Modules\Base\Models\SysOrganization;
use Exception;
use Illuminate\Http\JsonResponse;
use Siushin\Util\Traits\ParamTool;

/**
 * 控制器：组织架构
 * @module 组织架构
 */
class OrganizationController extends Controller
{
    use ParamTool;

    /**
     * 获取组织架构（树状结构）
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::index)]
    public function index(): JsonResponse
    {
        $params = trimParam(request()->all());
        return success(SysOrganization::getTreeData($params));
    }

    /**
     * 新增组织架构
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::add)]
    public function add(): JsonResponse
    {
        $params = trimParam(request()->all());
        return success(SysOrganization::addOrganization($params));
    }

    /**
     * 更新组织架构
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::update)]
    public function update(): JsonResponse
    {
        $params = trimParam(request()->only(['organization_id', 'organization_name', 'organization_pid']));
        return success(SysOrganization::updateOrganization($params));
    }

    /**
     * 删除组织架构
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::delete)]
    public function delete(): JsonResponse
    {
        $params = trimParam(request()->only(['organization_id']));
        return success(SysOrganization::deleteOrganization($params));
    }

    /**
     * 移动组织架构
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::move)]
    public function move(): JsonResponse
    {
        $params = trimParam(request()->all());
        return success(SysOrganization::moveOrganization($params));
    }
}
