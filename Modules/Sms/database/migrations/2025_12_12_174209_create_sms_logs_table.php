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
        // 构造枚举备注
        $sourceTypeComment = buildEnumComment(RequestSourceEnum::cases(), '访问来源');
        $smsTypeComment = buildEnumComment(SmsTypeEnum::cases(), '短信类型');

        Schema::create('sms_logs', function (Blueprint $table) use ($sourceTypeComment, $smsTypeComment) {
            $table->id()->comment('ID');
            $table->unsignedBigInteger('account_id')->nullable()->comment('账号ID');
            $table->string('source_type', 50)->comment($sourceTypeComment);
            $table->string('sms_type', 20)->comment($smsTypeComment);
            $table->string('phone', 11)->comment('手机号');
            $table->string('code', 6)->nullable()->comment('验证码（仅开发环境可见）');
            $table->tinyInteger('status')->default(1)->comment('发送状态:1成功,0失败');
            $table->string('error_message')->nullable()->comment('错误信息');
            $table->ipAddress()->comment('IP地址');
            $table->string('ip_location')->nullable()->comment('IP归属地');
            $table->integer('expire_minutes')->nullable()->comment('验证码过期时间（分钟）');
            $table->json('extend_data')->nullable()->comment('补充数据');
            $table->timestamp('created_at')->comment('创建时间');

            $table->index('account_id');
            $table->index('phone');
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

