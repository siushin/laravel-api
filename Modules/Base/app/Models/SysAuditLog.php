<?php

namespace Modules\Base\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Siushin\LaravelTool\Cases\Json;
use Siushin\LaravelTool\Traits\ModelTool;
use Siushin\Util\Traits\ParamTool;

/**
 * 模型：审计日志
 */
class SysAuditLog extends Model
{
    use HasFactory, ParamTool, ModelTool;

    protected $table = 'sys_audit_log';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'before_data' => Json::class,
            'after_data' => Json::class,
        ];
    }

    /**
     * 获取审计日志列表
     * @param array $params
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function getPageData(array $params = []): array
    {
        $data = self::fastGetPageData(self::query(), $params, [
            'account_id' => '=',
            'module' => 'like',
            'action' => '=',
            'resource_type' => '=',
            'resource_id' => '=',
            'date_range' => 'audited_at',
            'keyword' => ['module', 'description'],
        ]);

        // 关联账号信息
        $accountIds = array_values(array_unique(array_filter(array_column($data['data'], 'account_id'))));
        if (!empty($accountIds)) {
            $accounts = Account::query()
                ->whereIn('id', $accountIds)
                ->with('profile')
                ->select(['id', 'username'])
                ->get()
                ->keyBy('id')
                ->toArray();

            foreach ($data['data'] as &$item) {
                if (isset($accounts[$item['account_id']])) {
                    $account = $accounts[$item['account_id']];
                    $nickname = $account['profile']['nickname'] ?? '';
                    $username = $account['username'];
                    $item['username'] = $nickname ? "{$nickname}({$username})" : $username;
                } else {
                    $item['username'] = '';
                }
            }
        } else {
            foreach ($data['data'] as &$item) {
                $item['username'] = '';
            }
        }

        return $data;
    }
}
