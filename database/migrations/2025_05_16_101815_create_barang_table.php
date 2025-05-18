<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id(); // Ini akan menjadi kolom 'No' secara otomatis
            $table->string('nama_barang');
            $table->string('merek');
            $table->decimal('harga', 15, 2); // Kolom untuk harga, 15 digit dengan 2 desimal
            $table->string('satuan'); // Unit of measurement (pcs, kg, dll)
            $table->integer('stok');
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barang');
    }
}
