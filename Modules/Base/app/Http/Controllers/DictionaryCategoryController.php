<?php

namespace Modules\Base\Http\Controllers;

use Modules\Base\Attributes\OperationAction;
use Modules\Base\Enums\OperationActionEnum;
use Modules\Base\Models\SysDictionaryCategory;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * 控制器：数据字典分类
 * @module 数据字典
 */
class DictionaryCategoryController extends Controller
{
    /**
     * 数据字典分类列表（全部）
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::index)]
    public function index(): JsonResponse
    {
        return success(SysDictionaryCategory::getAllData());
    }
}
