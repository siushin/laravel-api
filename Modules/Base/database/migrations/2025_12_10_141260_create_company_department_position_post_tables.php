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
        // 公司表
        Schema::create('sys_company', function (Blueprint $table) {
            $table->id('company_id')->comment('公司ID');
            $table->string('company_name')->comment('公司名称');
            $table->string('company_code')->nullable()->comment('公司编码');
            $table->unsignedBigInteger('organization_id')->nullable()->comment('关联组织架构ID');
            $table->string('legal_person')->nullable()->comment('法人代表');
            $table->string('contact_phone')->nullable()->comment('联系电话');
            $table->string('contact_email')->nullable()->comment('联系邮箱');
            $table->string('address')->nullable()->comment('公司地址');
            $table->text('description')->nullable()->comment('公司描述');
            $table->tinyInteger('status')->default(1)->comment('状态：1正常，0禁用');
            $table->timestamps();
            $table->softDeletes();

            // 关联组织架构表
            $table->foreign('organization_id')
                ->references('organization_id')
                ->on('sys_organization')
                ->onDelete('set null');

            $table->index('organization_id');
            $table->index('company_code');
            $table->comment('公司表');
        });

        // 部门表
        Schema::create('sys_department', function (Blueprint $table) {
            $table->id('department_id')->comment('部门ID');
            $table->string('department_name')->comment('部门名称');
            $table->string('department_code')->nullable()->comment('部门编码');
            $table->unsignedBigInteger('company_id')->nullable()->comment('所属公司ID');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('上级部门ID');
            $table->string('full_parent_id')->nullable()->comment('完整上级部门ID路径');
            $table->string('manager_id')->nullable()->comment('部门负责人ID');
            $table->text('description')->nullable()->comment('部门描述');
            $table->tinyInteger('status')->default(1)->comment('状态：1正常，0禁用');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes();

            // 关联公司表
            $table->foreign('company_id')
                ->references('company_id')
                ->on('sys_company')
                ->onDelete('set null');

            $table->index('company_id');
            $table->index('parent_id');
            $table->index('department_code');
            $table->comment('部门表');
        });

        // 职位表
        Schema::create('sys_position', function (Blueprint $table) {
            $table->id('position_id')->comment('职位ID');
            $table->string('position_name')->comment('职位名称');
            $table->string('position_code')->nullable()->comment('职位编码');
            $table->unsignedBigInteger('department_id')->nullable()->comment('所属部门ID');
            $table->text('job_description')->nullable()->comment('职位描述');
            $table->text('job_requirements')->nullable()->comment('任职要求');
            $table->tinyInteger('status')->default(1)->comment('状态：1正常，0禁用');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes();

            // 关联部门表
            $table->foreign('department_id')
                ->references('department_id')
                ->on('sys_department')
                ->onDelete('set null');

            $table->index('department_id');
            $table->index('position_code');
            $table->comment('职位表');
        });

        // 岗位表
        Schema::create('sys_post', function (Blueprint $table) {
            $table->id('post_id')->comment('岗位ID');
            $table->string('post_name')->comment('岗位名称');
            $table->string('post_code')->nullable()->comment('岗位编码');
            $table->unsignedBigInteger('position_id')->nullable()->comment('所属职位ID');
            $table->unsignedBigInteger('department_id')->nullable()->comment('所属部门ID');
            $table->text('post_description')->nullable()->comment('岗位描述');
            $table->text('post_requirements')->nullable()->comment('岗位要求');
            $table->tinyInteger('status')->default(1)->comment('状态：1正常，0禁用');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes();

            // 关联职位表
            $table->foreign('position_id')
                ->references('position_id')
                ->on('sys_position')
                ->onDelete('set null');

            // 关联部门表
            $table->foreign('department_id')
                ->references('department_id')
                ->on('sys_department')
                ->onDelete('set null');

            $table->index('position_id');
            $table->index('department_id');
            $table->index('post_code');
            $table->comment('岗位表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_post');
        Schema::dropIfExists('sys_position');
        Schema::dropIfExists('sys_department');
        Schema::dropIfExists('sys_company');
    }
};

