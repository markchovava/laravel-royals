<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * 
     */
    public function up(): void
    {
        Schema::create('voucher_reward_useds', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('campaign_id')->nullable();
            $table->bigInteger('campaign_managed_id')->nullable();
            $table->bigInteger('reward_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_reward_useds');
    }
};
