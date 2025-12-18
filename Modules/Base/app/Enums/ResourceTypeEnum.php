<?php

namespace Modules\Base\Enums;

/**
 * 枚举：资源类型
 */
enum ResourceTypeEnum: string
{
    case user   = 'user';   // 用户
    case role   = 'role';   // 角色
    case menu   = 'menu';   // 菜单
    case config = 'config'; // 配置
    case file   = 'file';   // 文件
    case log    = 'log';    // 日志
    case other  = 'other';  // 其他
}
