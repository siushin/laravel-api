<?php

namespace App\Http\Controllers;

use App\Models\SysOrganization;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function add(Request $request): JsonResponse
    {
        $params = $request->all();
        return success(SysOrganization::addOrganization($params));
    }

    /**
     * 更新组织架构
     * @param Request $request
     * @param string  $id
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $params = array_merge($request->only(['organization_name']), ['organization_id' => $id]);
        return success(SysOrganization::updateOrganization($params));
    }

    /**
     * 删除组织架构
     * @param string $id
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function delete(string $id): JsonResponse
    {
        $params = ['organization_id' => $id];
        return success(SysOrganization::deleteOrganization($params));
    }

    /**
     * 移动组织架构
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function move(Request $request): JsonResponse
    {
        $params = $request->all();
        return success(SysOrganization::moveOrganization($params));
    }
}
