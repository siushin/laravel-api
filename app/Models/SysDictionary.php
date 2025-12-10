<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Siushin\LaravelTool\Cases\Json;
use Siushin\LaravelTool\Enums\LogActionEnum;
use Siushin\LaravelTool\Traits\ModelTool;
use Siushin\Util\Traits\ParamTool;

/**
 * 模型：字典
 */
class SysDictionary extends Model
{
    use HasFactory, ParamTool, ModelTool;

    protected $primaryKey = 'dictionary_id';
    protected $table      = 'sys_dictionary';

    protected $fillable = [
        'dictionary_id', 'category_id', 'dictionary_name', 'dictionary_value', 'parent_id', 'extend_data'
    ];

    protected function casts(): array
    {
        return [
            'extend_data' => Json::class,
        ];
    }

    // 自动生成值（按照序号自增）
    private static array $auto_ins_generate_value = [];

    // 自动生成值（值跟键相同）
    private static array $auto_ins_same_key_value = [];

    /**
     * 获取数据字典列表（分页）
     * @param array $params
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function getPageData(array $params = []): array
    {
        $category_id = SysDictionaryCategory::checkCodeValidate($params);
        $params['category_id'] = $category_id;
        $data = self::fastGetPageData(self::query(), $params, [
            'category_id' => '=',
            'parent_id' => '=',
            'dictionary_name' => 'like',
            'dictionary_value' => 'like',
            'time_range' => 'created_at',
        ], ['dictionary_id', 'dictionary_name', 'dictionary_value', 'parent_id', 'created_at']);
        return self::appendParentData($data);
    }

    /**
     * 获取数据字典列表（全部）
     * @param array $params 支持：category_code（指定字典类型code）
     * @param array $fields
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function getAllData(array $params = [], array $fields = []): array
    {
        $fields = $fields ?: ['dictionary_id', 'dictionary_name', 'dictionary_value', 'parent_id', 'created_at'];
        $category_id = SysDictionaryCategory::checkCodeValidate($params);
        $params['category_id'] = $category_id;
        $data = self::fastGetAllData(self::class, $params, [
            'category_id' => '=',
            'parent_id' => '=',
            'dictionary_name' => 'like',
            'dictionary_value' => 'like',
            'time_range' => 'created_at',
        ], $fields);
        return ($fields && in_array('parent_name', $fields)) ? self::appendParentData($data, 'all') : $data;
    }

    /**
     * 追加父级数据
     * @param array  $data
     * @param string $type
     * @return array
     * @author siushin<siushin@163.com>
     */
    public static function appendParentData(array &$data, string $type = 'page'): array
    {
        $parent_ids = array_unique(array_column($type == 'page' ? $data['data'] : $data, 'parent_id'));
        $parent_list = self::query()->whereIn('dictionary_id', $parent_ids)->pluck('dictionary_name', 'dictionary_id');
        $update_data = $type == 'page' ? $data['data'] : $data;
        foreach ($update_data as &$item) {
            $parent_name = $item['parent_id'] ? ($parent_list[$item['parent_id']] ?? '') : '';
            $item['parent_name'] = $parent_name;
        }
        $type == 'page' ? ($data['data'] = $update_data) : ($data = $update_data);
        return $data;
    }

    /**
     * 获取指定数据字典列表（根据code）
     * @param string $category_code
     * @param array  $fields
     * @param bool   $isValue2Key
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function getDictionaryByCode(string $category_code, array $fields = [], bool $isValue2Key = false): array
    {
        $fields = $fields ?: ['dictionary_name', 'dictionary_value'];
        $category_id = SysDictionaryCategory::checkCodeValidate(compact('category_code'));
        $data = self::fastGetAllData(self::query(), compact('category_id'), [
            'category_id' => '=',
        ], $fields);
        return $isValue2Key ? array_column($data, 'dictionary_name', 'dictionary_value') : $data;
    }

    /**
     * 根据code获取指定列数据
     * @param string $category_code
     * @param string $field
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function getDictionaryColumnData(string $category_code, string $field = 'dictionary_value'): array
    {
        return array_column(self::getDictionaryByCode($category_code), $field);
    }

    /**
     * 新增数据字典
     * @param array $params
     * @param array $response_keys
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function addDictionary(array $params = [], array $response_keys = []): array
    {
        $category_id = SysDictionaryCategory::checkCodeValidate($params);
        $params['category_id'] = $category_id;

        self::trimValueArray($params, [], [null]);
        self::checkEmptyParam($params, ['category_code', 'dictionary_name']);

        $category_code = $params['category_code'];
        $dictionary_name = $params['dictionary_name'];
        $parent_id = $params['parent_id'] = self::getIntValOrNull($params, 'parent_id') ?: 0;

        $check_where = compact('category_id', 'dictionary_name');
        !$parent_id && $check_where['parent_id'] = $parent_id;

        if (array_key_exists($category_code, self::$auto_ins_generate_value)) {
            $where = compact('category_id');
            // 判断是否有parent_id值
            !is_null($parent_id) && $where['parent_id'] = $parent_id;
            $last_max_info = self::query()->where($where)->selectRaw('max(cast(dictionary_value as SIGNED)) as dictionary_value')->first();
            // 自动生成值（取当前数据库最大值+1）
            $params['dictionary_value'] = !is_null($last_max_info->dictionary_value) ?
                intval($last_max_info->dictionary_value) + 1 : self::$auto_ins_generate_value[$category_code];
        } elseif (in_array($category_code, self::$auto_ins_same_key_value)) {
            $params['dictionary_value'] = $params['dictionary_name'];
            $check_where['dictionary_value'] = $params['dictionary_value'];
        } else {
            $check_where['dictionary_value'] = $params['dictionary_value'];
        }

        self::checkEmptyParam($params, ['dictionary_value']);
        $check = self::query()->where($check_where)->value('dictionary_id');
        $check && throw_exception('数据已存在');
        $params['created_at'] = date('Y-m-d H:i:s');
        $info = self::query()->create($params);
        !$info && throw_exception('添加数据字典失败');
        $info = $info->toArray();
        logging(LogActionEnum::insert->name, "添加数据字典失败(dictionary_name: {$params['dictionary_name']})", $info);
        $response_keys = $response_keys ?: ['dictionary_id', 'dictionary_name', 'dictionary_value', 'created_at'];
        return self::getArrayByKeys($info, $response_keys);
    }

    /**
     * 更新数据字典
     * @param array $params
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function updateDictionary(array $params = []): array
    {
        self::trimValueArray($params, [], [null]);
        $params = self::getArrayByKeys($params, ['dictionary_id', 'dictionary_name', 'dictionary_value', 'parent_id', 'extend_data']);

        self::checkEmptyParam($params, ['dictionary_id', 'dictionary_name']);

        $dictionary_id = $params['dictionary_id'];
        $dictionary_name = $params['dictionary_name'];
        $extend_data = self::getQueryParam($params, 'extend_data');

        $info = self::query()->find($dictionary_id);
        $old_data = $info->toArray();
        !$info && throw_exception('找不到该数据，请刷新后重试');

        $category_id = $info['category_id'];
        $parent_id = $info['parent_id'];
        $check_where = compact('category_id', 'dictionary_name');
        !$parent_id && $check_where['parent_id'] = $parent_id;
        $category_code = SysDictionaryCategory::query()->where('category_id', $info['category_id'])->value('category_code');
        $update_data = compact('dictionary_name');
        $extend_data && $update_data['extend_data'] = $extend_data;

        // 自动生成值（按照序号自增）
        if (array_key_exists($category_code, self::$auto_ins_generate_value)) {
            $update_data['dictionary_value'] = $info['dictionary_value'];
        } elseif (in_array($category_code, self::$auto_ins_same_key_value)) {
            // 自动生成值（值跟键相同）
            $check_where['dictionary_value'] = $update_data['dictionary_value'] = $params['dictionary_name'];
        } else {
            self::checkEmptyParam($params, ['dictionary_value']);
            $check_where['dictionary_value'] = $update_data['dictionary_value'] = $params['dictionary_value'];
        }

        $exist_check = self::query()->where($check_where)
            ->where('dictionary_id', '<>', $dictionary_id)
            ->value('dictionary_id');
        $exist_check && throw_exception('该数据已存在，更新失败');

        $bool = $info->update($update_data);
        !$bool && throw_exception('更新数据字典失败');

        $log_extend_data = compareArray($update_data, $old_data);
        logging(LogActionEnum::update->name, "更新数据字典(dictionary_name: {$params['dictionary_name']})", $log_extend_data);

        return [];
    }

    /**
     * 删除数据字典
     * @param array $params
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function deleteDictionary(array $params = []): array
    {
        self::checkEmptyParam($params, ['dictionary_id']);
        $info = self::query()->find($params['dictionary_id']);
        !$info && throw_exception('数据不存在');
        $bool = $info->delete();
        !$bool && throw_exception('删除失败');

        logging(LogActionEnum::delete->name, "删除数据字典(ID: {$info['dictionary_id']})", $info->toArray());

        return [];
    }

    /**
     * 批量删除数据字典
     * @param array $params 请求参数（需包含 dictionary_ids）
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function batchDeleteDictionary(array $params): array
    {
        self::checkEmptyParam($params, ['dictionary_ids']);

        $dictionary_ids = self::getQueryParam($params, 'dictionary_ids', [], ',');
        empty($dictionary_ids) && throw_exception('无效的字典ID列表');

        $records = self::query()->whereIn('dictionary_id', $dictionary_ids)->get();

        if ($records->count() != count($dictionary_ids)) {
            throw_exception('无效的字典ID列表，请刷新页面重试～');
        }

        $deletedCount = self::destroy($dictionary_ids);
        $deletedCount === 0 && throw_exception('删除失败，可能记录已不存在');

        logging(
            LogActionEnum::batchDelete->name,
            "批量删除数据字典(数量: $deletedCount, IDs: " . implode(',', $dictionary_ids) . ")",
            $records->toArray()
        );

        return [];
    }
}
