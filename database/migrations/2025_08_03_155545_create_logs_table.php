<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Siushin\LaravelTool\Enums\SysUserType;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id('log_id')->comment('日志ID');
            $table->char('source_type', 10)->comment('来源类型[' . enum_to_string_chain(SysUserType::cases()) . ']');
            $table->unsignedBigInteger('user_id')->nullable()->comment('用户ID');
            $table->char('action_type', 20)->comment('操作类型');
            $table->string('content')->comment('日志内容');
            $table->ipAddress()->comment('IP地址');
            $table->string('ip_location')->comment('IP归属地');
            $table->json('extend_data')->comment('补充数据');
            $table->timestamp('created_at')->comment('创建时间');

            $table->comment('日志表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
