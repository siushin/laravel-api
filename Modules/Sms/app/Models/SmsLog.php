<?php

namespace Modules\Sms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Sms\Enums\SmsTypeEnum;
use Siushin\LaravelTool\Cases\Json;
use Siushin\LaravelTool\Enums\RequestSourceEnum;
use Siushin\LaravelTool\Traits\ModelTool;
use Siushin\Util\Traits\ParamTool;

/**
 * 模型：短信发送记录
 */
class SmsLog extends Model
{
    use HasFactory, ParamTool, ModelTool;

    protected $table = 'sms_logs';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'sms_type'       => SmsTypeEnum::class,
            'source_type'    => RequestSourceEnum::class,
            'extend_data'    => Json::class,
            'status'         => 'integer',
            'expire_minutes' => 'integer',
        ];
    }
}

