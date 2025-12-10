<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Siushin\LaravelTool\Enums\RequestSourceEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $source_type_list = array_column(RequestSourceEnum::cases(), 'value');

        Schema::create('sys_logs', function (Blueprint $table) use ($source_type_list) {
            $table->id('log_id')->comment('日志ID');
            $table->unsignedBigInteger('account_id')->nullable()->comment('账号ID');
            $table->enum('source_type', $source_type_list)->comment('访问来源[' . enum_to_string_chain(RequestSourceEnum::cases()) . ']');
            $table->char('action_type', 20)->comment('操作类型');
            $table->string('content')->comment('日志内容');
            $table->ipAddress()->comment('IP地址');
            $table->string('ip_location')->comment('IP归属地');
            $table->json('extend_data')->comment('补充数据');
            $table->timestamp('created_at')->comment('创建时间');

            $table->comment('日志表');
        });

        Schema::create('sys_operation_log', function (Blueprint $table) use ($source_type_list) {
            $table->id()->comment('ID');
            $table->unsignedBigInteger('account_id')->nullable()->comment('账号ID');
            $table->enum('source_type', $source_type_list)->comment('访问来源[' . enum_to_string_chain(RequestSourceEnum::cases()) . ']');
            $table->string('module', 50)->comment('模块名称');
            $table->string('action', 50)->comment('操作类型');
            $table->string('method', 10)->comment('HTTP方法');
            $table->text('path')->comment('请求路径');
            $table->text('params')->nullable()->comment('请求参数');
            $table->ipAddress('ip')->comment('IP地址');
            $table->string('user_agent')->nullable()->comment('User-Agent');
            $table->timestamp('operated_at')->useCurrent()->comment('操作时间');

            $table->index('account_id');
            $table->index('module');
            $table->index('action');
            $table->index('operated_at');

            $table->comment('操作日志表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_logs');
        Schema::dropIfExists('sys_operation_log');
    }
};
