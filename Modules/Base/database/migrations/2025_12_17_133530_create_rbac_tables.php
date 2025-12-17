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
        // 角色表
        Schema::create('sys_role', function (Blueprint $table) {
            $table->id('role_id')->comment('角色ID');
            $table->string('role_name', 50)->comment('角色名称');
            $table->string('role_code', 50)->unique()->comment('角色编码');
            $table->string('description')->nullable()->comment('角色描述');
            $table->tinyInteger('status')->default(1)->comment('状态: 1启用, 0禁用');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes()->comment('软删除时间');

            $table->index('status');
            $table->index('sort');
            $table->comment('角色表');
        });

        // 菜单表
        Schema::create('sys_menu', function (Blueprint $table) {
            $table->id('menu_id')->comment('菜单ID');
            $table->string('menu_name', 50)->comment('菜单名称');
            $table->string('menu_path', 200)->nullable()->comment('路由路径');
            $table->string('menu_icon', 50)->nullable()->comment('图标名称');
            $table->enum('menu_type', ['menu', 'button'])->default('menu')->comment('类型: menu菜单, button按钮');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父菜单ID, 0表示顶级菜单');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态: 1启用, 0禁用');
            $table->timestamps();
            $table->softDeletes()->comment('软删除时间');

            $table->index('parent_id');
            $table->index('menu_type');
            $table->index('status');
            $table->index('sort');
            $table->comment('菜单表');
        });

        // 用户角色关联表
        Schema::create('sys_user_role', function (Blueprint $table) {
            $table->id()->comment('关联ID');
            $table->unsignedBigInteger('user_id')->comment('用户ID（关联bs_account.id）');
            $table->unsignedBigInteger('role_id')->comment('角色ID');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('bs_account')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('role_id')
                ->references('role_id')
                ->on('sys_role')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unique(['user_id', 'role_id'], 'uk_user_role');
            $table->index('user_id');
            $table->index('role_id');
            $table->comment('用户角色关联表');
        });

        // 角色菜单关联表
        Schema::create('sys_role_menu', function (Blueprint $table) {
            $table->id()->comment('关联ID');
            $table->unsignedBigInteger('role_id')->comment('角色ID');
            $table->unsignedBigInteger('menu_id')->comment('菜单ID');
            $table->timestamps();

            $table->foreign('role_id')
                ->references('role_id')
                ->on('sys_role')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('menu_id')
                ->references('menu_id')
                ->on('sys_menu')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unique(['role_id', 'menu_id'], 'uk_role_menu');
            $table->index('role_id');
            $table->index('menu_id');
            $table->comment('角色菜单关联表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_role_menu');
        Schema::dropIfExists('sys_user_role');
        Schema::dropIfExists('sys_menu');
        Schema::dropIfExists('sys_role');
    }
};
