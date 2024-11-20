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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('store_name');
            $table->string('address')->nullable();
            $table->string('opening_hours')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedTinyInteger('store_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
