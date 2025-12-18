<?php

namespace Modules\Base\Enums;

/**
 * 枚举：审计操作类型
 */
enum AuditActionEnum: string
{
    case permission_change = 'permission_change';   // 权限变更
    case role_assign       = 'role_assign';         // 角色分配
    case data_export       = 'data_export';         // 数据导出
    case config_modify     = 'config_modify';       // 配置修改
    case user_create       = 'user_create';         // 用户创建
    case user_update       = 'user_update';         // 用户更新
    case user_delete       = 'user_delete';         // 用户删除
    case role_create       = 'role_create';         // 角色创建
    case role_update       = 'role_update';         // 角色更新
    case role_delete       = 'role_delete';         // 角色删除
    case menu_create       = 'menu_create';         // 菜单创建
    case menu_update       = 'menu_update';         // 菜单更新
    case menu_delete       = 'menu_delete';         // 菜单删除
}
