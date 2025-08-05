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
        Schema::create('sys_files', function (Blueprint $table) {
            $table->id('file_id')->comment('文件ID');
            $table->char('file_name')->comment('文件名');
            $table->char('origin_file_name')->comment('原始文件名');
            $table->char('file_path')->unique()->comment('文件目录');
            $table->unsignedInteger('file_size')->comment('文件大小（以字节为单位）');
            $table->char('mime_type')->comment('文件的MIME类型');
            $table->char('file_ext_name')->comment('文件扩展名');
            $table->unsignedBigInteger('user_id')->default(0)->comment('用户ID（上传人）');
            $table->string('checksum', 64)->comment('文件的校验和（SHA-256哈希值）');
            $table->ulidMorphs('fileable'); // 一对一（多态）
            $table->timestamps();

            $table->comment('文件表');
        });

        Schema::create('sys_file_images', function (Blueprint $table) {
            $table->id('image_id')->comment('图片ID');
            $table->ulid('fileable_id')->comment('关联ID');
            $table->unsignedInteger('image_width')->comment('图片宽度（px）');
            $table->unsignedInteger('image_height')->comment('图片高度（px）');
            $table->timestamps();

            $table->comment('文件-附属信息-图片表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_files');
        Schema::dropIfExists('sys_file_images');
    }
};
