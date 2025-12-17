<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 模块表
        Schema::create('sys_module', function (Blueprint $table) {
            $table->id('module_id')->comment('模块ID');
            $table->string('module_identifier', 50)->unique()->comment('模块标识（用于表名前缀，如：sms, base）');
            $table->string('module_name', 50)->comment('模块名称（对应module.json中的name）');
            $table->string('module_alias', 100)->nullable()->comment('模块别名（对应module.json中的alias）');
            $table->text('module_description')->nullable()->comment('模块描述（对应module.json中的description）');
            $table->unsignedBigInteger('uploader_id')->nullable()->comment('上传人ID（关联bs_account.id）');
            $table->tinyInteger('status')->default(1)->comment('状态: 1启用, 0禁用');
            $table->unsignedInteger('priority')->default(0)->comment('优先级（对应module.json中的priority，数字越大优先级越高）');
            $table->string('version', 20)->nullable()->comment('模块版本号');
            $table->json('keywords')->nullable()->comment('关键词（对应module.json中的keywords）');
            $table->json('providers')->nullable()->comment('服务提供者（对应module.json中的providers）');
            $table->timestamps();
            $table->softDeletes()->comment('软删除时间');

            $table->foreign('uploader_id')
                ->references('id')
                ->on('bs_account')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->index('module_identifier');
            $table->index('module_name');
            $table->index('status');
            $table->index('priority');
            $table->index('uploader_id');
            $table->comment('模块管理表');
        });

        // 模块菜单关联表
        Schema::create('sys_module_menu', function (Blueprint $table) {
            $table->id()->comment('关联ID');
            $table->unsignedBigInteger('module_id')->comment('模块ID');
            $table->unsignedBigInteger('menu_id')->comment('菜单ID');
            $table->timestamps();

            $table->foreign('module_id')
                ->references('module_id')
                ->on('sys_module')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('menu_id')
                ->references('menu_id')
                ->on('sys_menu')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // 同一模块不能重复关联同一菜单
            $table->unique(['module_id', 'menu_id'], 'uk_module_menu');
            $table->index('module_id');
            $table->index('menu_id');
            $table->comment('模块菜单关联表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_module_menu');
        Schema::dropIfExists('sys_module');
    }
};
