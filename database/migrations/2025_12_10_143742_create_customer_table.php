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
        Schema::create('bs_customer', function (Blueprint $table) {
            $table->id()->comment('客户ID');
            $table->unsignedBigInteger('user_id')->unique()->comment('用户ID');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('bs_user')->onDelete('cascade');
            $table->comment('客户表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_customer');
    }
};

