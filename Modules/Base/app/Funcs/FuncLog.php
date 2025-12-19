<?php

/**
 * 助手函数：日志、调试（基于Laravel）
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Base\Services\LogService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 记录常规日志
 * @param string   $action_type
 * @param string   $content
 * @param array    $extend_data
 * @param int|null $account_id
 * @return bool
 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
 * @author siushin<siushin@163.com>
 */
function logGeneral(string $action_type, string $content, array $extend_data = [], ?int $account_id = null): bool
{
    try {
        $logService = app(LogService::class);
        return $logService->logGeneral($action_type, $content, $extend_data, $account_id);
    } catch (Exception $e) {
        Log::error('记录常规日志失败: ' . $e->getMessage());
        return false;
    }
}

/**
 * 记录登录日志
 * @param Request     $request    请求对象
 * @param int|null    $account_id 账号ID
 * @param string|null $username   用户名
 * @param int         $status     登录状态：1成功，0失败
 * @param string|null $message    登录信息/错误信息
 * @return bool
 * @author siushin<siushin@163.com>
 */
function logLogin(Request $request, ?int $account_id = null, ?string $username = null, int $status = 1, ?string $message = null): bool
{
    try {
        $logService = app(LogService::class);
        return $logService->logLogin($request, $account_id, $username, $status, $message);
    } catch (Exception $e) {
        Log::error('记录登录日志失败: ' . $e->getMessage());
        return false;
    }
}

/**
 * 记录操作日志
 * @param Request    $request        请求对象
 * @param int|null   $account_id     账号ID
 * @param string     $module         模块名称
 * @param string     $action         操作类型（如：create, update, delete, export）
 * @param string     $method         HTTP方法
 * @param string     $path           请求路径
 * @param array|null $params         请求参数
 * @param int|null   $response_code  响应状态码
 * @param int|null   $execution_time 执行耗时（毫秒）
 * @return bool
 * @author siushin<siushin@163.com>
 */
function logOperation(
    Request $request,
    ?int    $account_id = null,
    string  $module = '',
    string  $action = '',
    string  $method = '',
    string  $path = '',
    ?array  $params = null,
    ?int    $response_code = null,
    ?int    $execution_time = null
): bool
{
    try {
        $logService = app(LogService::class);
        return $logService->logOperation($request, $account_id, $module, $action, $method, $path, $params, $response_code, $execution_time);
    } catch (Exception $e) {
        Log::error('记录操作日志失败: ' . $e->getMessage());
        return false;
    }
}

/**
 * 记录审计日志
 * @param Request     $request       请求对象
 * @param int|null    $account_id    操作人ID
 * @param string      $module        模块名称
 * @param string      $action        操作类型（如：权限变更、角色分配、数据导出、配置修改）
 * @param string|null $resource_type 资源类型（如：user, role, menu, config）
 * @param int|null    $resource_id   资源ID
 * @param array|null  $before_data   变更前数据
 * @param array|null  $after_data    变更后数据
 * @param string|null $description   操作描述
 * @return bool
 * @author siushin<siushin@163.com>
 */
function logAudit(
    Request $request,
    ?int    $account_id = null,
    string  $module = '',
    string  $action = '',
    ?string $resource_type = null,
    ?int    $resource_id = null,
    ?array  $before_data = null,
    ?array  $after_data = null,
    ?string $description = null
): bool
{
    try {
        $logService = app(LogService::class);
        return $logService->logAudit($request, $account_id, $module, $action, $resource_type, $resource_id, $before_data, $after_data, $description);
    } catch (Exception $e) {
        Log::error('记录审计日志失败: ' . $e->getMessage());
        return false;
    }
}
