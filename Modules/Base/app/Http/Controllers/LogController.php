<?php

namespace Modules\Base\Http\Controllers;

use Modules\Base\Attributes\OperationAction;
use Modules\Base\Enums\BrowserEnum;
use Modules\Base\Enums\DeviceTypeEnum;
use Modules\Base\Enums\HttpMethodEnum;
use Modules\Base\Enums\LogActionEnum;
use Modules\Base\Enums\OperationActionEnum;
use Modules\Base\Enums\OperatingSystemEnum;
use Modules\Base\Enums\ResourceTypeEnum;
use Modules\Base\Models\SysAuditLog;
use Modules\Base\Models\SysLog;
use Modules\Base\Models\SysLoginLog;
use Modules\Base\Models\SysOperationLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Siushin\LaravelTool\Enums\RequestSourceEnum;

/**
 * 控制器：日志
 * @module 日志管理
 */
class LogController extends Controller
{
    /**
     * 常规日志列表（分页）
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::index)]
    public function index(Request $request): JsonResponse
    {
        $params = trimParam($request->all());
        return success(SysLog::getPageData($params));
    }

    /**
     * 操作日志列表（分页）
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::index)]
    public function operationLog(Request $request): JsonResponse
    {
        $params = trimParam($request->all());
        return success(SysOperationLog::getPageData($params));
    }

    /**
     * 登录日志列表（分页）
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::index)]
    public function loginLog(Request $request): JsonResponse
    {
        $params = trimParam($request->all());
        return success(SysLoginLog::getPageData($params));
    }

    /**
     * 审计日志列表（分页）
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::index)]
    public function auditLog(Request $request): JsonResponse
    {
        $params = trimParam($request->all());
        return success(SysAuditLog::getPageData($params));
    }

    /**
     * 来源类型列表
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getSourceTypeList(): JsonResponse
    {
        $list = [];
        foreach (RequestSourceEnum::cases() as $case) {
            $list[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }
        return success($list);
    }

    /**
     * 日志操作类型列表（常规日志）
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getActionList(): JsonResponse
    {
        $list = [];
        foreach (LogActionEnum::cases() as $case) {
            $list[] = [
                'label' => $case->value,
                'value' => $case->name,
            ];
        }
        return success($list);
    }

    /**
     * 操作类型列表（操作日志）
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getOperationActionList(): JsonResponse
    {
        $list = [];
        foreach (OperationActionEnum::cases() as $case) {
            $list[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }
        return success($list);
    }

    /**
     * HTTP方法列表
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getHttpMethodList(): JsonResponse
    {
        $list = [];
        foreach (HttpMethodEnum::cases() as $case) {
            $list[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }
        return success($list);
    }

    /**
     * 浏览器列表
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getBrowserList(): JsonResponse
    {
        $list = [];
        foreach (BrowserEnum::cases() as $case) {
            $list[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }
        return success($list);
    }

    /**
     * 操作系统列表
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getOperatingSystemList(): JsonResponse
    {
        $list = [];
        foreach (OperatingSystemEnum::cases() as $case) {
            $list[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }
        return success($list);
    }

    /**
     * 设备类型列表
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getDeviceTypeList(): JsonResponse
    {
        $list = [];
        foreach (DeviceTypeEnum::cases() as $case) {
            $list[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }
        return success($list);
    }

    /**
     * 审计操作类型列表
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getAuditActionList(): JsonResponse
    {
        $list = [];
        foreach (OperationActionEnum::cases() as $case) {
            $list[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }
        return success($list);
    }

    /**
     * 资源类型列表
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getResourceTypeList(): JsonResponse
    {
        $list = [];
        foreach (ResourceTypeEnum::cases() as $case) {
            $list[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }
        return success($list);
    }

    /**
     * 获取操作日志模块名称列表（去重）
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getModuleList(): JsonResponse
    {
        $modules = SysOperationLog::query()
            ->distinct()
            ->whereNotNull('module')
            ->where('module', '!=', '')
            ->orderBy('module')
            ->pluck('module')
            ->toArray();

        $list = [];
        foreach ($modules as $module) {
            $list[] = [
                'label' => $module,
                'value' => $module,
            ];
        }
        return success($list);
    }

    /**
     * 获取操作日志响应状态码列表（去重）
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getResponseCodeList(): JsonResponse
    {
        $responseCodes = SysOperationLog::query()
            ->distinct()
            ->whereNotNull('response_code')
            ->orderBy('response_code')
            ->pluck('response_code')
            ->toArray();

        $list = [];
        foreach ($responseCodes as $code) {
            $list[] = [
                'label' => (string)$code,
                'value' => $code,
            ];
        }
        return success($list);
    }

    /**
     * 获取操作日志搜索框所需的所有选项数据
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getOperationLogSearchOptions(): JsonResponse
    {
        // 模块名称列表（去重）
        $modules = SysOperationLog::query()
            ->distinct()
            ->whereNotNull('module')
            ->where('module', '!=', '')
            ->orderBy('module')
            ->pluck('module')
            ->toArray();

        $moduleList = [];
        foreach ($modules as $module) {
            $moduleList[] = [
                'label' => $module,
                'value' => $module,
            ];
        }

        // 操作类型列表
        $operationActionList = [];
        foreach (OperationActionEnum::cases() as $case) {
            $operationActionList[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }

        // HTTP方法列表
        $httpMethodList = [];
        foreach (HttpMethodEnum::cases() as $case) {
            $httpMethodList[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }

        // 响应状态码列表（去重）
        $responseCodes = SysOperationLog::query()
            ->distinct()
            ->whereNotNull('response_code')
            ->orderBy('response_code')
            ->pluck('response_code')
            ->toArray();

        $responseCodeList = [];
        foreach ($responseCodes as $code) {
            $responseCodeList[] = [
                'label' => (string)$code,
                'value' => $code,
            ];
        }

        // 访问来源列表
        $sourceTypeList = [];
        foreach (RequestSourceEnum::cases() as $case) {
            $sourceTypeList[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }

        return success([
            'module' => $moduleList,
            'action' => $operationActionList,
            'method' => $httpMethodList,
            'response_code' => $responseCodeList,
            'source_type' => $sourceTypeList,
        ]);
    }

    /**
     * 获取常规日志搜索框所需的所有选项数据
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getIndexSearchData(): JsonResponse
    {
        // 操作类型列表（常规日志）
        $actionList = [];
        foreach (LogActionEnum::cases() as $case) {
            $actionList[] = [
                'label' => $case->value,
                'value' => $case->name,
            ];
        }

        // 访问来源列表
        $sourceTypeList = [];
        foreach (RequestSourceEnum::cases() as $case) {
            $sourceTypeList[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }

        return success([
            'action_type' => $actionList,
            'source_type' => $sourceTypeList,
        ]);
    }

    /**
     * 获取操作日志搜索框所需的所有选项数据
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getOperationLogSearchData(): JsonResponse
    {
        // 模块名称列表（去重）
        $modules = SysOperationLog::query()
            ->distinct()
            ->whereNotNull('module')
            ->where('module', '!=', '')
            ->orderBy('module')
            ->pluck('module')
            ->toArray();

        $moduleList = [];
        foreach ($modules as $module) {
            $moduleList[] = [
                'label' => $module,
                'value' => $module,
            ];
        }

        // 操作类型列表
        $operationActionList = [];
        foreach (OperationActionEnum::cases() as $case) {
            $operationActionList[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }

        // HTTP方法列表
        $httpMethodList = [];
        foreach (HttpMethodEnum::cases() as $case) {
            $httpMethodList[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }

        // 响应状态码列表（去重）
        $responseCodes = SysOperationLog::query()
            ->distinct()
            ->whereNotNull('response_code')
            ->orderBy('response_code')
            ->pluck('response_code')
            ->toArray();

        $responseCodeList = [];
        foreach ($responseCodes as $code) {
            $responseCodeList[] = [
                'label' => (string)$code,
                'value' => $code,
            ];
        }

        // 访问来源列表
        $sourceTypeList = [];
        foreach (RequestSourceEnum::cases() as $case) {
            $sourceTypeList[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }

        return success([
            'module' => $moduleList,
            'action' => $operationActionList,
            'method' => $httpMethodList,
            'response_code' => $responseCodeList,
            'source_type' => $sourceTypeList,
        ]);
    }

    /**
     * 获取登录日志搜索框所需的所有选项数据
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getLoginLogSearchData(): JsonResponse
    {
        // 浏览器列表
        $browserList = [];
        foreach (BrowserEnum::cases() as $case) {
            $browserList[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }

        // 操作系统列表
        $osList = [];
        foreach (OperatingSystemEnum::cases() as $case) {
            $osList[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }

        // 设备类型列表
        $deviceTypeList = [];
        foreach (DeviceTypeEnum::cases() as $case) {
            $deviceTypeList[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }

        // 登录状态列表（固定值）
        $statusList = [
            ['label' => '成功', 'value' => 1],
            ['label' => '失败', 'value' => 0],
        ];

        return success([
            'browser' => $browserList,
            'operating_system' => $osList,
            'device_type' => $deviceTypeList,
            'status' => $statusList,
        ]);
    }

    /**
     * 获取审计日志搜索框所需的所有选项数据
     * @return JsonResponse
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getAuditLogSearchData(): JsonResponse
    {
        // 模块名称列表（从审计日志表去重）
        $modules = SysAuditLog::query()
            ->distinct()
            ->whereNotNull('module')
            ->where('module', '!=', '')
            ->orderBy('module')
            ->pluck('module')
            ->toArray();

        $moduleList = [];
        foreach ($modules as $module) {
            $moduleList[] = [
                'label' => $module,
                'value' => $module,
            ];
        }

        // 操作类型列表
        $actionList = [];
        foreach (OperationActionEnum::cases() as $case) {
            $actionList[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }

        // 资源类型列表
        $resourceTypeList = [];
        foreach (ResourceTypeEnum::cases() as $case) {
            $resourceTypeList[] = [
                'label' => $case->value,
                'value' => $case->value,
            ];
        }

        return success([
            'module' => $moduleList,
            'action' => $actionList,
            'resource_type' => $resourceTypeList,
        ]);
    }
}
