<?php

namespace Modules\Base\Attributes;

use Attribute;

/**
 * 字典描述属性
 * 用于在枚举 case 上标记描述信息
 *
 * 使用示例：
 * #[DictionaryDescription('记录请求发起的渠道或终端，如 PC 端、移动端、第三方接口等')]
 * case RequestSource = '请求来源';
 */
#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class DictionaryDescription
{
    public function __construct(
        public string $description
    )
    {
    }
}
