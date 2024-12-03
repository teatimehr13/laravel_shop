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
        Schema::table('subcategories', function (Blueprint $table) {
            // 新增 category_id 和 order_index 的組合唯一鍵， 如5->1， 6->1，5->2
            $table->unique(['category_id', 'order_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropUnique(['category_id', 'order_index']);
        });
    }
};
