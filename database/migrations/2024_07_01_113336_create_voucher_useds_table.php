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
    **/
    public function up(): void
    {
        Schema::create('voucher_useds', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->bigInteger('points')->nullable();
            $table->bigInteger('voucher_generated_id')->nullable();
            $table->bigInteger('campaign_managed_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_useds');
    }
};
