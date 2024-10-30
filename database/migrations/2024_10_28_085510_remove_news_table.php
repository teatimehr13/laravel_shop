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
        Schema::dropIfExists('news');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('date');
            $table->string('image')->nullable();
            $table->text('description');
            $table->boolean('enable')->default(0);
            $table->timestamps();
        });
    }
};
