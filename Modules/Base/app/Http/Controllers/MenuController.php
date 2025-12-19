<?php

namespace Modules\Base\Http\Controllers;

use Modules\Base\Attributes\OperationAction;
use Modules\Base\Enums\OperationActionEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Base\Models\SysMenu;
use Modules\Base\Enums\AccountTypeEnum;

/**
 * 控制器：菜单
 * @module 菜单管理
 */
class MenuController extends Controller
{
    /**
     * 获取用户菜单列表
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::view)]
    public function getUserMenus(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            throw_exception('用户未登录');
        }

        // 判断是否为超级管理员（通过关联关系直接获取）
        $isSuperAdmin = $user->account_type === AccountTypeEnum::Admin
            && $user->adminInfo?->is_super == 1;

        // 超级管理员返回所有 account_type 为 admin 的菜单
        if ($isSuperAdmin) {
            $menus = SysMenu::where('account_type', AccountTypeEnum::Admin->value)
                ->where('status', 1)
                ->orderBy('sort', 'asc')
                ->orderBy('menu_id', 'asc')
                ->get()
                ->toArray();
        } else {
            // 普通用户根据角色获取菜单
            $menuIds = DB::table('sys_user_role')
                ->join('sys_role_menu', 'sys_user_role.role_id', '=', 'sys_role_menu.role_id')
                ->where('sys_user_role.user_id', $user->id)
                ->pluck('sys_role_menu.menu_id')
                ->toArray();

            // 获取必须选中的菜单（is_required = 1）
            $requiredMenuIds = SysMenu::where('account_type', $user->account_type->value)
                ->where('is_required', 1)
                ->where('status', 1)
                ->pluck('menu_id')
                ->toArray();

            // 合并必须选中的菜单和角色分配的菜单
            $allMenuIds = array_unique(array_merge($menuIds, $requiredMenuIds));

            if (empty($allMenuIds)) {
                $menus = [];
            } else {
                $menus = SysMenu::whereIn('menu_id', $allMenuIds)
                    ->where('status', 1)
                    ->orderBy('sort', 'asc')
                    ->orderBy('menu_id', 'asc')
                    ->get()
                    ->toArray();
            }
        }

        // 转换为树形结构
        $menuTree = $this->buildMenuTree($menus);

        return success($menuTree, '获取菜单成功');
    }

    /**
     * 构建菜单树形结构
     * @param array $menus
     * @param int   $parentId
     * @return array
     */
    private function buildMenuTree(array $menus, int $parentId = 0): array
    {
        $tree = [];

        foreach ($menus as $menu) {
            if ($menu['parent_id'] == $parentId) {
                $menuItem = [
                    'path'      => $menu['menu_path'],
                    'name'      => $menu['menu_key'],
                    'title'     => $menu['menu_name'],
                    'icon'      => $menu['menu_icon'],
                    'component' => $menu['component'],
                    'redirect'  => $menu['redirect'],
                    'layout'    => $menu['layout'],
                    'access'    => $menu['access'],
                ];

                // 处理 wrappers
                if (!empty($menu['wrappers'])) {
                    $wrappers = json_decode($menu['wrappers'], true);
                    if (is_array($wrappers)) {
                        $menuItem['wrappers'] = $wrappers;
                    }
                }

                // 移除null和空字符串字段（但保留false和0）
                $filteredMenuItem = array_filter($menuItem, function ($value) {
                    return $value !== null && $value !== '';
                });
                $menuItem = $filteredMenuItem;

                // 递归获取子菜单
                $children = $this->buildMenuTree($menus, $menu['menu_id']);
                if (!empty($children)) {
                    $menuItem['routes'] = $children;
                }

                $tree[] = $menuItem;
            }
        }

        return $tree;
    }
}
