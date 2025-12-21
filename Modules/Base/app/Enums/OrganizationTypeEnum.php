<?php

namespace Modules\Base\Enums;

/**
 * 枚举：组织架构类型
 */
enum OrganizationTypeEnum: string
{
    case Default = 'default';   // 默认
    case Country = 'country';   // 国家
    case Company = 'company';   // 公司
    case Branch  = 'branch';    // 分公司
    case BU      = 'bu';        // 事业部
}
