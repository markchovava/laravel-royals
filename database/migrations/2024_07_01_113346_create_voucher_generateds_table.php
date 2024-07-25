<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 
     * Run the migrations.
     * 
     */
    public function up(): void
    {
        Schema::create('voucher_generateds', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('campaign_managed_id')->nullable();
            $table->string('code')->nullable();
            $table->integer('status')->nullable();
            $table->string('receipt_no')->nullable();
            $table->string('points')->nullable();
            $table->bigInteger('amount')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_generateds');
    }
};
