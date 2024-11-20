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
        Schema::create('product_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('type');
            $table->unsignedInteger('order');
            $table->string('link_text')->nullable();
            $table->string('link_url')->nullable();
            $table->date('promo_start_date')->nullable();
            $table->date('promo_end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_descriptions');
    }
};
