<?php

namespace Modules\Base\Models;

use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Siushin\LaravelTool\Cases\Json;
use Modules\Base\Enums\LogActionEnum;
use Siushin\LaravelTool\Enums\RequestSourceEnum;
use Siushin\LaravelTool\Traits\ModelTool;
use Siushin\Util\Traits\ParamTool;

/**
 * 模型：系统日志
 */
class SysLog extends Model
{
    use HasFactory, ParamTool, ModelTool;

    protected $primaryKey = 'log_id';
    protected $table      = 'sys_logs';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'extend_data' => Json::class,
        ];
    }

    protected $hidden = ['extend_data'];

    protected function sourceType(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => RequestSourceEnum::from($value)->value,
        );
    }

    protected function actionType(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => LogActionEnum::{$value}->value,
        );
    }

    /**
     * 获取日志列表
     * @param array $params
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function getPageData(array $params = []): array
    {
        $data = self::fastGetPageData(self::query(), $params, [
            'source_type' => '=',
            'action_type' => '=',
            'keyword'     => 'content',
            'date_range'  => 'created_at'
        ]);

        $user_ids = array_values(array_unique(array_column($data['data'], 'user_id')));
        $user_list = Account::query()
            ->whereIn('id', $user_ids)
            ->with('profile')
            ->select(['username', 'id'])
            ->get()
            ->toArray();
        $user_list = array_column($user_list, null, 'id');

        foreach ($data['data'] as &$item) {
            if (isset($user_list[$item['user_id']])) {
                $nickname = $user_list[$item['user_id']]['profile']['nickname'] ?? '';
                $username = $user_list[$item['user_id']]['username'];
                $item['username'] = $nickname ? "{$nickname}({$username})" : $username;
            } else {
                $item['username'] = '';
            }
            unset($item['user_id']);
        }

        return $data;
    }
}
