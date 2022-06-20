<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnBannerProduk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_produk', function (Blueprint $table) {
            $table->text('deskripsi_produk')->nullable();
            $table->text('foto_produk')->nullable();
            $table->text('banner_produk')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_produk', function (Blueprint $table) {
            $table->dropColumn('deskripsi_produk');
            $table->dropColumn('foto_produk');
            $table->dropColumn('banner_produk');
        });
    }
}
