<?php

namespace Modules\Base\Attributes;

use Attribute;
use Modules\Base\Enums\OperationActionEnum;

/**
 * 操作类型属性
 * 用于在控制器方法上标记操作类型，便于IDE解析
 *
 * 使用示例：
 * #[OperationAction(OperationActionEnum::index)]
 * public function index(): JsonResponse
 */
#[Attribute(Attribute::TARGET_METHOD)]
class OperationAction
{
    public function __construct(
        public OperationActionEnum $action
    )
    {
    }
}
