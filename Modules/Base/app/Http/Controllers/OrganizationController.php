<?php

namespace Modules\Base\Http\Controllers;

use Modules\Base\Models\SysOrganization;
use Exception;
use Illuminate\Http\JsonResponse;
use Siushin\Util\Traits\ParamTool;

/**
 * 控制器：组织架构
 */
class OrganizationController extends Controller
{
    use ParamTool;

    /**
     * 获取组织架构（树状结构）
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    public function index(): JsonResponse
    {
        $params = request()->all();
        return success(SysOrganization::getTreeData($params));
    }

    /**
     * 新增组织架构
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function add(): JsonResponse
    {
        $params = request()->all();
        return success(SysOrganization::addOrganization($params));
    }

    /**
     * 更新组织架构
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function update(): JsonResponse
    {
        $params = request()->only(['organization_id', 'organization_name', 'organization_pid']);
        return success(SysOrganization::updateOrganization($params));
    }

    /**
     * 删除组织架构
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function delete(): JsonResponse
    {
        $params = request()->only(['organization_id']);
        return success(SysOrganization::deleteOrganization($params));
    }

    /**
     * 移动组织架构
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function move(): JsonResponse
    {
        $params = request()->all();
        return success(SysOrganization::moveOrganization($params));
    }
}
