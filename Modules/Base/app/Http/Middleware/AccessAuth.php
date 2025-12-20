<?php

namespace Modules\Base\Http\Middleware;

use Modules\Base\Enums\AccountTypeEnum;
use Closure;
use Illuminate\Http\Request;
use Siushin\LaravelTool\Enums\RequestSourceEnum;
use Symfony\Component\HttpFoundation\Response;

/**
 * 中间件：访问权限控制
 */
class AccessAuth
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();

        // 默认请求来源（Web端）
        $requestSource = RequestSourceEnum::web->value;

        // 根据路由动态分配请求来源
        if (str_starts_with($path, 'api/admin/')) {
            $requestSource = RequestSourceEnum::admin_api->value;
        } elseif (str_starts_with($path, 'api/we/')) {
            $requestSource = RequestSourceEnum::wechat_mini->value;
        } elseif (str_starts_with($path, 'api/user/')) {
            $requestSource = RequestSourceEnum::api->value;
        }

        // 根据路由判断账号类型：api/admin/ 开头为 Admin，其他为 Customer
        $accountType = str_starts_with($path, 'api/admin/')
            ? AccountTypeEnum::Admin->value
            : AccountTypeEnum::User->value;

        // 将请求来源和账号类型注入请求
        $request->merge([
            'request_source' => $requestSource,
            'account_type' => $accountType,
        ]);

        // 普通用户访问时，自动追加账号ID
        $this->appendAccountIdForUser($request);

        return $next($request);
    }

    /**
     * 为普通用户自动追加账号ID
     *
     * @param Request $request
     * @return void
     */
    private function appendAccountIdForUser(Request $request): void
    {
        $user = $request->user();

        // 如果用户未认证，直接返回
        if (!$user) {
            return;
        }

        // 从认证用户中获取账号类型
        $accountType = $user->account_type;

        // 如果不是普通用户，直接返回
        if ($accountType !== AccountTypeEnum::User) {
            return;
        }

        // 追加 account_id 参数，如果已存在则覆盖（确保普通用户只能查看自己的数据）
        $request->merge([
            'account_id' => $user->id,
        ]);
    }
}
