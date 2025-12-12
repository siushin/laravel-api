<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Sms\Enums\SmsTypeEnum;
use Siushin\LaravelTool\Enums\RequestSourceEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $smsTypeList = array_column(SmsTypeEnum::cases(), 'value');
        $sourceTypeList = array_column(RequestSourceEnum::cases(), 'value');

        // 生成短信类型注释
        $getEnumComment = function (SmsTypeEnum $case): ?string {
            $reflection = new \ReflectionClass(SmsTypeEnum::class);
            $file = $reflection->getFileName();
            if ($file && file_exists($file)) {
                $lines = file($file);
                foreach ($lines as $line) {
                    if (preg_match('/case\s+' . preg_quote($case->name, '/') . '\s*=\s*[\'"]' . preg_quote($case->value, '/') . '[\'"]\s*;\s*\/\/\s*(.+)/', $line, $matches)) {
                        return trim($matches[1]);
                    }
                }
            }
            return null;
        };

        $commentParts = [];
        foreach (SmsTypeEnum::cases() as $case) {
            $description = $getEnumComment($case);
            if ($description) {
                $commentParts[] = $case->value . ':' . $description;
            } else {
                $commentParts[] = $case->value;
            }
        }
        $smsTypeComment = '短信类型[' . implode(',', $commentParts) . ']';

        Schema::create('sms_logs', function (Blueprint $table) use ($smsTypeList, $sourceTypeList, $smsTypeComment) {
            $table->id()->comment('ID');
            $table->unsignedBigInteger('account_id')->nullable()->comment('账号ID');
            $table->enum('source_type', $sourceTypeList)->comment('访问来源[' . enum_to_string_chain(RequestSourceEnum::cases()) . ']');
            $table->enum('sms_type', $smsTypeList)->comment($smsTypeComment);
            $table->string('mobile', 11)->comment('手机号');
            $table->string('code', 6)->nullable()->comment('验证码（仅开发环境可见）');
            $table->tinyInteger('status')->default(1)->comment('发送状态:1成功,0失败');
            $table->string('error_message')->nullable()->comment('错误信息');
            $table->ipAddress()->comment('IP地址');
            $table->string('ip_location')->nullable()->comment('IP归属地');
            $table->integer('expire_minutes')->nullable()->comment('验证码过期时间（分钟）');
            $table->json('extend_data')->nullable()->comment('补充数据');
            $table->timestamp('created_at')->comment('创建时间');

            $table->index('account_id');
            $table->index('mobile');
            $table->index('sms_type');
            $table->index('status');
            $table->index('created_at');
            $table->comment('短信发送记录表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};

