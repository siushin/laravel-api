<?php

use App\Enums\AccountTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bs_user', function (Blueprint $table) {
            $table->id()->comment('用户ID');
            $table->string('username', 50)->unique()->comment('用户名');
            $table->string('password')->comment('密码');
            $table->enum('account_type', array_column(AccountTypeEnum::cases(), 'value'))
                ->default(AccountTypeEnum::Customer->value)
                ->comment('账号类型[' . enum_to_string_chain(AccountTypeEnum::cases()) . ']');
            $table->tinyInteger('status')->default(1)->comment('状态:1正常,0禁用');
            $table->string('last_login_ip', 50)->nullable()->comment('最后登录IP');
            $table->timestamp('last_login_time')->nullable()->comment('最后登录时间');
            $table->timestamps();
            $table->softDeletes()->comment('软删除时间');

            $table->index('status');
            $table->index('account_type');
            $table->comment('用户表');
        });

        Schema::create('sys_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sys_sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_user');
        Schema::dropIfExists('sys_password_reset_tokens');
        Schema::dropIfExists('sys_sessions');
    }
};
