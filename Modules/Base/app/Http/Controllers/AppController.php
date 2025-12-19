<?php

namespace Modules\Base\Http\Controllers;

use Modules\Base\Attributes\OperationAction;
use Modules\Base\Enums\OperationActionEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Nwidart\Modules\Facades\Module;

/**
 * 控制器：应用管理
 * @module 应用管理
 */
class AppController extends Controller
{
    /**
     * 获取我的应用列表
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::query)]
    public function getMyApps(Request $request): JsonResponse
    {
        $modulesPath = base_path('Modules');
        $apps = [];

        if (!File::exists($modulesPath)) {
            return success([], '暂无应用');
        }

        // 遍历 Modules 目录
        $directories = File::directories($modulesPath);

        foreach ($directories as $directory) {
            $moduleName = basename($directory);
            $moduleJsonPath = $directory . '/module.json';

            // 检查 module.json 是否存在
            if (!File::exists($moduleJsonPath)) {
                continue;
            }

            try {
                // 读取 module.json 内容
                $moduleJsonContent = File::get($moduleJsonPath);
                $moduleData = json_decode($moduleJsonContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    continue;
                }

                // 获取模块信息
                $module = Module::find($moduleName);
                $isEnabled = $module ? $module->isEnabled() : false;

                // 构建应用数据
                $app = [
                    'name'        => $moduleData['name'] ?? $moduleName,
                    'alias'       => $moduleData['alias'] ?? $moduleName,
                    'description' => $moduleData['description'] ?? '',
                    'keywords'    => $moduleData['keywords'] ?? [],
                    'priority'    => $moduleData['priority'] ?? 0,
                    'enabled'     => $isEnabled,
                    'path'        => $moduleName,
                ];

                $apps[] = $app;
            } catch (Exception $e) {
                // 跳过无法读取的模块
                continue;
            }
        }

        // 按 priority 排序，priority 越大越靠前
        usort($apps, function ($a, $b) {
            return ($b['priority'] ?? 0) <=> ($a['priority'] ?? 0);
        });

        return success($apps, '获取应用列表成功');
    }
}
