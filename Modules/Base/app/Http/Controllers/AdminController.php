<?php

namespace Modules\Base\Http\Controllers;

use Modules\Base\Attributes\OperationAction;
use Modules\Base\Enums\OperationActionEnum;
use Modules\Base\Models\Admin;
use Exception;
use Illuminate\Http\JsonResponse;
use Siushin\Util\Traits\ParamTool;

/**
 * 控制器：管理员管理
 * @module 管理员管理
 */
class AdminController extends Controller
{
    use ParamTool;

    /**
     * 获取管理员列表（分页）
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::index)]
    public function index(): JsonResponse
    {
        $params = trimParam(request()->all());
        return success(Admin::getPageData($params));
    }

    /**
     * 新增管理员
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::add)]
    public function add(): JsonResponse
    {
        $params = trimParam(request()->all());
        return success(Admin::addAdmin($params));
    }

    /**
     * 更新管理员
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::update)]
    public function update(): JsonResponse
    {
        $params = trimParam(request()->all());
        return success(Admin::updateAdmin($params));
    }

    /**
     * 删除管理员
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::delete)]
    public function delete(): JsonResponse
    {
        $params = trimParam(request()->only(['id']));
        return success(Admin::deleteAdmin($params));
    }
}

