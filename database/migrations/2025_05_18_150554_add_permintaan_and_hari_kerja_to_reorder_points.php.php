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
        Schema::table('reorder_points', function (Blueprint $table) {
            $table->decimal('permintaan_per_periode', 10, 2)->default(0)->after('lead_time');
            $table->integer('total_hari_kerja')->default(1)->after('permintaan_per_periode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reorder_points', function (Blueprint $table) {
            $table->dropColumn('permintaan_per_periode');
            $table->dropColumn('total_hari_kerja');
        });
    }
};
