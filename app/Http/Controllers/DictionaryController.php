<?php

namespace App\Http\Controllers;

use App\Models\SysDictionary;
use App\Models\SysDictionaryCategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Siushin\Util\Traits\ParamTool;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * 控制器：字典
 */
class DictionaryController extends Controller
{
    use ParamTool;

    /**
     * 数据字典列表（分页）
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function index(Request $request): JsonResponse
    {
        $params = $request->all();
        return success(SysDictionary::getPageData($params));
    }

    /**
     * 数据字典列表（全部）
     * @param Request $request
     * @param array   $fields
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function all(Request $request, array $fields = []): JsonResponse
    {
        $params = $request->all();
        return success(SysDictionary::getAllData($params, $fields));
    }

    /**
     * 新增数据字典
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function add(Request $request): JsonResponse
    {
        $params = $request->all();
        return success(SysDictionary::addDictionary($params));
    }

    /**
     * 编辑数据字典
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function update(Request $request): JsonResponse
    {
        $params = $request->all();
        return success(SysDictionary::updateDictionary($params));
    }

    /**
     * 删除数据字典
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function delete(Request $request): JsonResponse
    {
        $params = $request->all();
        return success(SysDictionary::deleteDictionary($params));
    }

    /**
     * 批量删除数据字典
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function batchDelete(Request $request): JsonResponse
    {
        $params = $request->all();
        return success(SysDictionary::batchDeleteDictionary($params));
    }

    /**
     * 下载数据字典模板
     * @param Request $request
     * @return BinaryFileResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function getTplFile(Request $request): BinaryFileResponse
    {
        $params = $request->all();
        [$category_name, $tpl_path] = SysDictionaryCategory::getDictionaryTempFilePath($params);
        return response()->download($tpl_path, "{$category_name}_模板文件.xlsx");
    }

    /**
     * 根据字典类型返回所有父级列表数据
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function getPidData(Request $request): JsonResponse
    {
        $params = $request->all();
        $category_id = SysDictionaryCategory::checkCodeValidate($params);
        $parent_ids = SysDictionary::query()->where(compact('category_id'))->distinct()->pluck('parent_id');
        $list = SysDictionary::query()->whereIn('category_id', $parent_ids)->pluck('dictionary_name', 'dictionary_id')->toArray();
        return success($list);
    }
}
