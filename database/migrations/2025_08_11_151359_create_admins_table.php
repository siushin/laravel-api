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
        Schema::create('sys_admins', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique()->comment('用户名');
            $table->string('real_name', 50)->nullable()->comment('姓名');
            $table->string('mobile', 11)->nullable()->unique()->comment('手机号');
            $table->string('email')->nullable()->unique()->comment('邮箱');
            $table->string('password')->comment('密码');
            $table->string('company_id')->nullable()->comment('所属公司ID');
            $table->string('department_id')->nullable()->comment('所属部门ID');
            $table->string('avatar')->nullable()->comment('头像');
            $table->tinyInteger('status')->default(1)->comment('状态:1正常,0禁用');
            $table->string('last_login_ip', 50)->nullable()->comment('最后登录IP');
            $table->timestamp('last_login_time')->nullable()->comment('最后登录时间');
            $table->rememberToken()->comment('记住我token');
            $table->softDeletes()->comment('软删除时间');
            $table->timestamps();

            $table->index('status');
            $table->comment('管理员表');
        });

        Schema::create('sys_role', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('角色名称');
            $table->string('code', 50)->unique()->comment('角色标识');
            $table->tinyInteger('status')->default(1)->comment('状态:1正常,0禁用');
            $table->integer('sort')->default(0)->comment('排序');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();

            $table->index('status');
            $table->comment('角色表');
        });

        Schema::create('sys_admin_role', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->comment('管理员ID');
            $table->unsignedBigInteger('role_id')->comment('角色ID');

            $table->primary(['admin_id', 'role_id']);
            $table->comment('管理员角色关联表');
        });

        Schema::create('sys_menu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级ID');
            $table->string('name', 50)->comment('菜单名称');
            $table->string('path')->nullable()->comment('路由路径');
            $table->string('component')->nullable()->comment('组件路径');
            $table->string('permission')->nullable()->comment('权限标识');
            $table->string('icon')->nullable()->comment('图标');
            $table->tinyInteger('type')->comment('类型:1目录,2菜单,3按钮');
            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('visible')->default(1)->comment('是否可见:1是,0否');
            $table->timestamps();

            $table->index('parent_id');
            $table->index('type');
            $table->comment('菜单表');
        });

        Schema::create('sys_role_menu', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->comment('角色ID');
            $table->unsignedBigInteger('menu_id')->comment('菜单ID');

            $table->primary(['role_id', 'menu_id']);
            $table->comment('角色菜单关联表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_admins');
        Schema::dropIfExists('sys_role');
        Schema::dropIfExists('sys_admin_role');
        Schema::dropIfExists('sys_menu');
        Schema::dropIfExists('sys_role_menu');
    }
};
