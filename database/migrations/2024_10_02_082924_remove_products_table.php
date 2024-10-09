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
        Schema::dropIfExists('products');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcategory_id');
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('description');
            $table->unsignedTinyInteger('published_status');
            $table->timestamps();
        });
    }
};
