<?php

namespace App\Http\Middleware;

use App\Enums\AccountTypeEnum;
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

        // 默认请求来源（匿名访问）
        $requestSource = RequestSourceEnum::guest->value;

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

        return $next($request);
    }
}
