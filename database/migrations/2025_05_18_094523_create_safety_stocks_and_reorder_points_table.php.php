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
        Schema::create('safety_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id');
            $table->decimal('pemakaian_maksimum', 12, 2);
            $table->decimal('pemakaian_rata_rata', 12, 2);
            $table->decimal('lead_time', 8, 2);
            $table->decimal('hasil', 12, 2)->nullable();
            $table->timestamps();

            $table->foreign('barang_id')->references('id')->on('barang')->onDelete('cascade');
        });
        Schema::create('reorder_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id');
            $table->decimal('safety_stock', 12, 2);
            $table->decimal('lead_time', 8, 2);
            $table->decimal('hasil', 12, 2)->nullable();
            $table->timestamps();

            $table->foreign('barang_id')->references('id')->on('barang')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reorder_points');
        Schema::dropIfExists('safety_stocks');
    }
};
