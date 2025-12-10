<?php

namespace Modules\Base\Http\Controllers;

use Modules\Base\Models\SysDictionaryCategory;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * 控制器：数据字典分类
 */
class DictionaryCategoryController extends Controller
{
    /**
     * 数据字典分类列表（全部）
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function index(): JsonResponse
    {
        return success(SysDictionaryCategory::getAllData());
    }
}
