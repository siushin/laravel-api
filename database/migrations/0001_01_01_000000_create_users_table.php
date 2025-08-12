<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Siushin\LaravelTool\Enums\SysGenderType;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('用户ID');
            $table->string('username')->comment('用户名');
            $table->string('nick_name')->comment('昵称');
            $table->enum('gender', array_column(SysGenderType::cases(), 'name'))
                ->default(SysGenderType::male->name)
                ->comment('性别[' . enum_to_string_chain(SysGenderType::cases()) . ']');
            $table->string('email')->nullable()->comment('邮箱')->unique();
            $table->timestamp('email_verified_at')->nullable()->comment('邮箱二次确认');
            $table->char('phone', 11)->unique()->comment('手机号码');
            $table->string('password')->comment('密码');
            $table->rememberToken()->comment('记住用户');
            $table->timestamps();

            $table->comment('用户表');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
