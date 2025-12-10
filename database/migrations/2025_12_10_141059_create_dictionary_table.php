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
        Schema::create('sys_dictionary_category', function (Blueprint $table) {
            $table->id('category_id')->comment('数据字典分类ID');
            $table->string('category_name')->comment('数据字典分类名');
            $table->string('category_code')->comment('数据字典编码');
            $table->string('tpl_path')->nullable()->default('')->comment('模板文件路径');
            $table->timestamps();

            $table->unique('category_name');
            $table->unique('category_code');

            $table->comment('数据字典分类表');
        });

        Schema::create('sys_dictionary', function (Blueprint $table) {
            $table->id('dictionary_id')->comment('数据字典ID');
            $table->unsignedBigInteger('category_id')->comment('字典类型ID');
            $table->foreign('category_id')->references('category_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete()
                ->on('sys_dictionary_category');
            $table->string('dictionary_name')->comment('名称');
            $table->string('dictionary_value')->comment('值');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父ID');
            $table->json('extend_data')->nullable()->comment('扩展数据');
            $table->unique(['category_id', 'dictionary_name', 'dictionary_value', 'parent_id'], 'unique_dictionary');
            $table->timestamps();

            $table->comment('数据字典表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_dictionary_category');
        Schema::dropIfExists('sys_dictionary');
    }
};
