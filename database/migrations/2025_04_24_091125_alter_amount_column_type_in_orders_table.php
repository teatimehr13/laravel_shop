<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change(); // 999.00
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('amount')->change(); // 回到原本的狀態
        });
    }
};
