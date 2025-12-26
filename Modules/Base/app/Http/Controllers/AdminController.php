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

    /**
     * 获取管理员详情
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getDetail(): JsonResponse
    {
        $params = trimParam(request()->only(['id']));
        return success(Admin::getAdminDetail($params));
    }

    /**
     * 获取管理员日志（支持多种日志类型）
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getLogs(): JsonResponse
    {
        $params = trimParam(request()->all());
        $logType = $params['log_type'] ?? 'general'; // general, operation, audit, login

        // 必须提供 account_id
        if (empty($params['account_id'])) {
            throw_exception('缺少 account_id 参数');
        }

        $accountId = $params['account_id'];
        $requestParams = array_merge($params, ['account_id' => $accountId]);

        switch ($logType) {
            case 'general':
                return success(\Modules\Base\Models\GeneralLog::getPageData($requestParams));
            case 'operation':
                return success(\Modules\Base\Models\OperationLog::getPageData($requestParams));
            case 'audit':
                return success(\Modules\Base\Models\AuditLog::getPageData($requestParams));
            case 'login':
                return success(\Modules\Base\Models\LoginLog::getPageData($requestParams));
            default:
                throw_exception('不支持的日志类型: ' . $logType);
        }
    }
}

