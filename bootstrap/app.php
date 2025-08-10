<?php

use App\Http\Middleware\AccessAuth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 跳过所有CSRF保护
        $middleware->validateCsrfTokens(except: ['*']);
        $middleware->append(AccessAuth::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 将 所有 异常 呈现为 JSON
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return true;
        });

        // 鉴权 异常
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($e->getMessage() == 'Unauthenticated.') {
                $data = ['code' => 401, 'message' => 'token已过期'];
                return response()->json($data);
            }
            return response()->json($e->getMessage());
        });

        // 常规 异常
        $exceptions->render(function (Exception $e, Request $request) {
            if (json_validate($e->getMessage())) {
                return response()->json(json_decode($e->getMessage()));
            }

            if ($e->getMessage() == 'Route [login] not defined.') {
                $data = ['code' => 401, 'message' => '无效token，请重新登录'];
                return response()->json($data);
            }

            return throw_exception($e->getMessage());
        });
    })->create();
