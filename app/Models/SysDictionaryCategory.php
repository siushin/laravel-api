<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Siushin\LaravelTool\Traits\ModelTool;
use Siushin\Util\Traits\ParamTool;

/**
 * 模型：字典分类
 */
class SysDictionaryCategory extends Model
{
    use HasFactory, ParamTool, ModelTool;

    protected $primaryKey = 'category_id';
    protected $table      = 'sys_dictionary_category';

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * 获取数据字典类型列表（全部）
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function getAllData(): array
    {
        $params['sortbys'] = 'category_id=asc';
        return self::fastGetAllData(self::query(), $params);
    }

    /**
     * 获取数据字典模板文件路径
     * @param array $params
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function getDictionaryTempFilePath(array $params = []): array
    {
        self::checkCodeValidate($params);
        $data = self::query()->where('category_code', $params['category_code'])->first();
        return [$data['category_name'], $data['tpl_path']];
    }

    /**
     * 检查Code参数有效性
     * @param array $params 支持参数：category_code
     * @return int  有效则返回字典类型ID，无效抛异常
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function checkCodeValidate(array $params = []): int
    {
        self::checkEmptyParam($params, ['category_code']);
        $category_code = $params['category_code'];
        $category_id = self::query()->where('category_code', $category_code)->value('category_id');
        !$category_id && throw_exception('category_code参数有误');
        return $category_id;
    }
}
