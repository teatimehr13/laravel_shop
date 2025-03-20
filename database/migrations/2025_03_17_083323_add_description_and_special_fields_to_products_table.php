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
        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->text('special_message')->nullable()->after('description'); 
            $table->dateTime('special_start_at')->nullable()->after('special_message'); 
            $table->dateTime('special_end_at')->nullable()->after('special_start_at');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->text('special_message')->nullable()->after('description'); 
            $table->dateTime('special_start_at')->nullable()->after('special_message'); 
            $table->dateTime('special_end_at')->nullable()->after('special_start_at');  
        });
    }
};
