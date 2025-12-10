<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Siushin\LaravelTool\Enums\GenderTypeEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bs_user_profile', function (Blueprint $table) {
            $table->id()->comment('用户资料ID');
            $table->unsignedBigInteger('user_id')->unique()->comment('用户ID');
            $table->string('real_name')->nullable()->comment('姓名');
            $table->enum('gender', array_column(GenderTypeEnum::cases(), 'name'))
                ->default(GenderTypeEnum::male->name)
                ->comment('性别[' . enum_to_string_chain(GenderTypeEnum::cases()) . ']');
            $table->string('avatar')->nullable()->comment('头像');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('bs_user')->onDelete('cascade');
            $table->comment('用户资料表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_user_profile');
    }
};

