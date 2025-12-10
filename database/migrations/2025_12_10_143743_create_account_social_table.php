<?php

use Illuminate\Database\Migrations\Migration;
use Siushin\LaravelTool\Enums\SocialTypeEnum;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 从枚举注释中提取中文描述的辅助函数
        $getEnumComment = function (SocialTypeEnum $case): ?string {
            $reflection = new \ReflectionClass(SocialTypeEnum::class);
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
        foreach (SocialTypeEnum::cases() as $case) {
            $description = $getEnumComment($case);
            if ($description) {
                $commentParts[] = $case->value . ':' . $description;
            } else {
                $commentParts[] = $case->value;
            }
        }
        $socialTypeComment = '社交类型[' . implode(',', $commentParts) . ']';

        Schema::create('bs_account_social', function (Blueprint $table) use ($socialTypeComment) {
            $table->id()->comment('社交网络ID');
            $table->unsignedBigInteger('user_id')->comment('账号ID');
            $table->enum('social_type', array_column(SocialTypeEnum::cases(), 'value'))
                ->comment($socialTypeComment);
            $table->string('social_account', 100)->comment('社交账号');
            $table->string('social_name', 50)->nullable()->comment('社交昵称');
            $table->string('avatar')->nullable()->comment('头像');
            $table->tinyInteger('is_verified')->default(0)->comment('是否已验证:1是,0否');
            $table->timestamp('verified_at')->nullable()->comment('验证时间');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('bs_account')->onDelete('cascade');
            $table->index(['user_id', 'social_type']);
            $table->unique(['user_id', 'social_type', 'social_account'], 'account_social_unique');
            $table->comment('账号社交网络表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_account_social');
    }
};
