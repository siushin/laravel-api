<?php

namespace App\Enums;

/**
 * 枚举：用户账号类型
 */
enum AccountTypeEnum: string
{
    case Admin    = 'admin';        // 管理员
    case Customer = 'customer';     // 客户
}

