<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pengaturan')->create('menu', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('kode')->nullable();
            $table->string('nama')->nullable();
            $table->string('url')->nullable();
            $table->string('path')->nullable();
            $table->string('icon')->nullable();
            $table->string('platform')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pengaturan')->dropIfExists('menu');
    }
}
