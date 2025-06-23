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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status')
                ->default('pending')
                ->comment('付款狀態：pending/paid/refunded');

            $table->string('fulfilment_status')
                ->default('pending')
                ->comment('物流狀態：pending/shipped/delivered/cancelled/returned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
             $table->dropColumn('payment_status');
             $table->dropColumn('fulfilment_status');
        });
    }
};
