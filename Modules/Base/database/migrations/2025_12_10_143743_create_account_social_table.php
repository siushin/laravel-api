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
        $socialTypeComment = buildEnumComment(SocialTypeEnum::cases(), '社交类型');

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
            $table->unique(['social_type', 'social_account'], 'account_social_unique');
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
