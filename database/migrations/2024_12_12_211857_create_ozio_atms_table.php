<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ozio_atms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("qty_1")->default(0);
            $table->unsignedBigInteger("qty_5")->default(0);
            $table->unsignedBigInteger("qty_10")->default(0);
            $table->unsignedBigInteger("qty_20")->default(0);
            $table->unsignedBigInteger("qty_50")->default(0);
            $table->unsignedBigInteger("qty_100")->default(0);
            $table->unsignedBigInteger("qty_200")->default(0);
            $table->unsignedBigInteger("qty_500")->default(0);
            $table->decimal("total_amount")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ozio_atms');
    }
};
