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
        Schema::table('safety_stocks', function (Blueprint $table) {
            $table->string('bulan', 7)->after('hasil')->comment('Format: MM/YYYY');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safety_stocks', function (Blueprint $table) {
            $table->dropColumn('bulan');
        });
    }
};
