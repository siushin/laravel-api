<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Siushin\LaravelTool\Enums\SysUserType;
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

        // 默认用户类型（访客）
        $userType = SysUserType::guest->name;

        // 根据路由动态分配用户类型
        if (str_starts_with($path, 'api/admin/')) {
            $userType = SysUserType::admin->name;
        } elseif (str_starts_with($path, 'api/we/')) {
            $userType = SysUserType::we->name;
        } elseif (str_starts_with($path, 'api/user/')) {
            $userType = SysUserType::user->name;
        }

        // 将用户类型注入请求
        $request->merge(['user_type' => $userType]);

        return $next($request);
    }
}
