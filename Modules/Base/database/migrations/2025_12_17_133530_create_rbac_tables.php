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
        $accountTypeComment = buildEnumComment(AccountTypeEnum::cases(), '账号类型');

        // 角色表
        Schema::create('sys_role', function (Blueprint $table) use ($accountTypeComment) {
            $table->id('role_id')->comment('角色ID');
            $table->enum('account_type', array_column(AccountTypeEnum::cases(), 'value'))
                ->default(AccountTypeEnum::Admin->value)
                ->comment($accountTypeComment);
            $table->string('role_name', 50)->comment('角色名称');
            $table->string('role_code', 50)->comment('角色编码');
            $table->string('description')->nullable()->comment('角色描述');
            $table->tinyInteger('status')->default(1)->comment('状态: 1启用, 0禁用');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes()->comment('软删除时间');

            // 同一账号类型下角色编码唯一
            $table->unique(['account_type', 'role_code'], 'uk_role_account_type_code');
            $table->index('account_type');
            $table->index('status');
            $table->index('sort');
            $table->comment('角色表');
        });

        // 菜单表
        Schema::create('sys_menu', function (Blueprint $table) use ($accountTypeComment) {
            $table->id('menu_id')->comment('菜单ID');
            $table->enum('account_type', array_column(AccountTypeEnum::cases(), 'value'))
                ->default(AccountTypeEnum::Admin->value)
                ->comment($accountTypeComment);
            $table->string('menu_name', 50)->comment('菜单名称');
            $table->string('name', 100)->nullable()->comment('菜单名称key（用于国际化，如：dashboard.workplace）');
            $table->string('menu_path', 200)->nullable()->comment('路由路径');
            $table->string('component', 200)->nullable()->comment('组件路径（相对路径，如：./Dashboard/Workplace）');
            $table->string('menu_icon', 50)->nullable()->comment('图标名称');
            $table->enum('menu_type', ['menu', 'button'])->default('menu')->comment('类型: menu菜单, button按钮');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父菜单ID, 0表示顶级菜单');
            $table->string('redirect', 200)->nullable()->comment('重定向路径');
            $table->boolean('layout')->nullable()->comment('是否使用布局: true使用, false不使用, null默认');
            $table->string('access', 100)->nullable()->comment('权限控制（如：canAdmin）');
            $table->text('wrappers')->nullable()->comment('包装组件（JSON数组格式）');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态: 1启用, 0禁用');
            $table->timestamps();
            $table->softDeletes()->comment('软删除时间');

            // 同一账号类型下，路径唯一（如果路径不为空）
            // 注意：parent_id=0时表示顶级菜单，parent_id>0时表示子菜单
            $table->index('account_type');
            $table->index('parent_id');
            $table->index('menu_type');
            $table->index('status');
            $table->index('sort');
            $table->index('name');
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

            // 同一用户不能重复分配同一角色
            // 注意：应用层需要校验用户账号类型与角色账号类型一致
            // admin用户只能分配account_type='admin'的角色
            // user用户只能分配account_type='user'的角色
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

            // 注意：应用层需要校验角色账号类型与菜单账号类型一致
            // admin角色只能关联account_type='admin'的菜单
            // user角色只能关联account_type='user'的菜单
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
