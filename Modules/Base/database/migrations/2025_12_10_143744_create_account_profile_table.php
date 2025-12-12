<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Base\Enums\VerificationMethodEnum;
use Siushin\LaravelTool\Enums\GenderTypeEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bs_account_profile', function (Blueprint $table) {
            $table->id()->comment('账号资料ID');
            $table->unsignedBigInteger('user_id')->unique()->comment('账号ID');
            $table->string('nickname')->nullable()->comment('昵称');
            $table->enum('gender', array_column(GenderTypeEnum::cases(), 'name'))
                ->default(GenderTypeEnum::male->name)
                ->comment('性别[' . enum_to_string_chain(GenderTypeEnum::cases()) . ']');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('real_name')->nullable()->comment('姓名（身份认证）');
            $table->string('id_card', 18)->nullable()->comment('身份证号码');
            $table->enum('verification_method', array_column(VerificationMethodEnum::cases(), 'value'))
                ->nullable()
                ->comment(buildEnumComment(VerificationMethodEnum::cases(), '认证方式'));
            $table->timestamp('verified_at')->nullable()->comment('认证时间');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('bs_account')->onDelete('cascade');
            $table->comment('账号资料表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_account_profile');
    }
};
