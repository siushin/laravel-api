<?php

use Modules\Base\Enums\AccountTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 从枚举注释中提取中文描述的辅助函数
        $getEnumComment = function (AccountTypeEnum $case): ?string {
            $reflection = new \ReflectionClass(AccountTypeEnum::class);
            $file = $reflection->getFileName();
            if ($file && file_exists($file)) {
                $lines = file($file);
                // 查找匹配的 case 行
                foreach ($lines as $line) {
                    // 匹配 case CaseName = 'value'; // 中文描述
                    if (preg_match('/case\s+' . preg_quote($case->name, '/') . '\s*=\s*[\'"]' . preg_quote($case->value, '/') . '[\'"]\s*;\s*\/\/\s*(.+)/', $line, $matches)) {
                        return trim($matches[1]);
                    }
                }
            }
            return null;
        };

        // 生成带中文描述的注释字符串
        $commentParts = [];
        foreach (AccountTypeEnum::cases() as $case) {
            $description = $getEnumComment($case);
            if ($description) {
                $commentParts[] = $case->value . ':' . $description;
            } else {
                $commentParts[] = $case->value;
            }
        }
        $accountTypeComment = '账号类型[' . implode(',', $commentParts) . ']';

        Schema::create('bs_account', function (Blueprint $table) use ($accountTypeComment) {
            $table->id()->comment('账号ID');
            $table->enum('account_type', array_column(AccountTypeEnum::cases(), 'value'))
                ->default(AccountTypeEnum::User->value)
                ->comment($accountTypeComment);
            $table->string('username', 50)->unique()->comment('用户名');
            $table->string('password')->comment('密码');
            $table->tinyInteger('status')->default(1)->comment('状态:1正常,0禁用');
            $table->string('last_login_ip', 50)->nullable()->comment('最后登录IP');
            $table->timestamp('last_login_time')->nullable()->comment('最后登录时间');
            $table->timestamps();
            $table->softDeletes()->comment('软删除时间');

            $table->index('status');
            $table->index('account_type');
            $table->comment('账号表');
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
        Schema::dropIfExists('bs_account');
        Schema::dropIfExists('sys_password_reset_tokens');
        Schema::dropIfExists('sys_sessions');
    }
};
