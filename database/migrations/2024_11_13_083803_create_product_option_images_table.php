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
        Schema::create('product_option_images', function (Blueprint $table) {
            $table->id();
            // 產品選項外鍵
            $table->foreignId('product_option_id')
                ->constrained('product_options')
                ->onDelete('cascade');  // 當產品選項被刪除時，同步刪除關聯的圖片

            // 產品圖片外鍵
            $table->foreignId('product_image_id')
                ->constrained('product_images')
                ->onDelete('cascade');  // 當產品圖片被刪除時，同步刪除關聯的記錄
            $table->timestamps();

            $table->unique(['product_option_id', 'product_image_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_option_images');
    }
};
