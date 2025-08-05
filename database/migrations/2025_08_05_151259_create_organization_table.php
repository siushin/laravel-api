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
        Schema::create('sys_organization', function (Blueprint $table) {
            $table->id('organization_id')->comment('组织架构ID');
            $table->char('organization_name')->comment('组织架构名称');
            $table->unsignedBigInteger('organization_pid')->comment('上级组织架构ID');
            $table->char('full_organization_pid')->comment('完整上级组织架构ID');
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
