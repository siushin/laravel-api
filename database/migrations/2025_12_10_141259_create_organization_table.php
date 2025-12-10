<?php

use App\Enums\OrganizationTypeEnum;
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
        $getEnumComment = function (OrganizationTypeEnum $case): ?string {
            $reflection = new \ReflectionClass(OrganizationTypeEnum::class);
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
        foreach (OrganizationTypeEnum::cases() as $case) {
            $description = $getEnumComment($case);
            if ($description) {
                $commentParts[] = $case->value . ':' . $description;
            } else {
                $commentParts[] = $case->value;
            }
        }
        $organizationTypeComment = '组织架构类型[' . implode(',', $commentParts) . ']';

        Schema::create('sys_organization', function (Blueprint $table) use ($organizationTypeComment) {
            $table->id('organization_id')->comment('组织架构ID');
            $table->char('organization_name')->comment('组织架构名称');
            $table->unsignedBigInteger('organization_pid')->comment('上级组织架构ID');
            $table->char('full_organization_pid')->comment('完整上级组织架构ID');
            $table->enum('organization_type', array_column(OrganizationTypeEnum::cases(), 'value'))
                ->default(OrganizationTypeEnum::Default->value)
                ->comment($organizationTypeComment);
            $table->timestamps();

            $table->comment('组织架构表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_organization');
    }
};
