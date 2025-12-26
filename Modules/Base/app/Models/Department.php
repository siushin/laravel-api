<?php

namespace Modules\Base\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Siushin\LaravelTool\Traits\ModelTool;
use Siushin\Util\Traits\ParamTool;

/**
 * 模型：部门
 */
class Department extends Model
{
    use ParamTool, ModelTool, SoftDeletes;

    protected $table      = 'gpa_department';
    protected $primaryKey = 'department_id';

    protected $hidden = [
        'deleted_at',
    ];

    const int STATUS_DISABLE = 0;   // 禁用
    const int STATUS_NORMAL  = 1;   // 正常

    /**
     * 获取部门列表（全部）
     * @param array $params 支持：department_code、department_name
     * @param array $fields
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function getAllData(array $params = [], array $fields = []): array
    {
        self::checkEmptyParam($params, ['company_id']);
        $params['status'] = self::STATUS_NORMAL;
        return self::fastGetAllData(self::class, $params, [
            'company_id'      => '=',
            'department_code' => '=',
            'department_name' => 'like',
            'status'          => '=',
        ], $fields);
    }
}
