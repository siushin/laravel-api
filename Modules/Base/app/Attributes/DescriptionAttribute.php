<?php

namespace Modules\Base\Attributes;

use Attribute;

/**
 * 描述注解
 * 用于在任何代码元素（类、方法、属性、枚举case等）上标记描述信息
 *
 * 使用示例：
 * #[DescriptionAttribute('记录请求发起的渠道或终端，如 PC 端、移动端、第三方接口等')]
 * case RequestSource = '请求来源';
 *
 * #[DescriptionAttribute('用户登录接口')]
 * public function login(): JsonResponse
 * @author siushin<siushin@163.com>
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS_CONSTANT | Attribute::IS_REPEATABLE)]
class DescriptionAttribute
{
    public function __construct(
        public string $description
    )
    {
    }
}

