<?php

namespace Modules\Base\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Siushin\LaravelTool\Traits\ModelTool;

/**
 * 模型：公司
 */
class Company extends Model
{
    use ModelTool, SoftDeletes;

    protected $table      = 'gpa_company';
    protected $primaryKey = 'company_id';

    protected $hidden = [
        'deleted_at',
    ];

    const int STATUS_DISABLE = 0;   // 禁用
    const int STATUS_NORMAL  = 1;   // 正常

    /**
     * 获取公司列表（全部）
     * @param array $params 支持：company_code、company_name
     * @param array $fields
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function getAllData(array $params = [], array $fields = []): array
    {
        $params['status'] = self::STATUS_NORMAL;
        // TODO 组织架构ID筛选
        return self::fastGetAllData(self::class, $params, [
            'company_code' => '=',
            'company_name' => 'like',
            'status'       => '=',
        ], $fields);
    }
}
