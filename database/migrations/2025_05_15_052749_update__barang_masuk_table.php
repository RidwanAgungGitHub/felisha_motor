<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBarangMasukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            // First check if the old supplier text column exists
            if (Schema::hasColumn('barang_masuk', 'supplier')) {
                $table->dropColumn('supplier');
            }

            // Only add supplier_id if it doesn't exist
            if (!Schema::hasColumn('barang_masuk', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')->after('barang_id');
                $table->foreign('supplier_id')->references('id')->on('suppliers');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            // Check if the foreign key constraint exists
            if (Schema::hasColumn('barang_masuk', 'supplier_id')) {
                // Drop the foreign key first
                $table->dropForeign(['supplier_id']);
                $table->dropColumn('supplier_id');
            }

            // Add back the old supplier column if it doesn't exist
            if (!Schema::hasColumn('barang_masuk', 'supplier')) {
                $table->string('supplier')->nullable();
            }
        });
    }
}
