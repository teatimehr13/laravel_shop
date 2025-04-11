<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('return_number')->unique()->nullable(); // 退貨單號
            $table->integer('total_subtotal')->default(0); //退款品項總額
            $table->integer('total_refund_amount')->default(0); //應扣除之費用
            $table->integer('total_deduct_amount')->default(0); //最終實退金額
            $table->string('status')->default('pending');
            $table->string('refund_method')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
