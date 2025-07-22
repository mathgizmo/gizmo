<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagUnitPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_unit', function (Blueprint $table) {
            $table->unsignedInteger('unit_id');
            $table->unsignedInteger('tag_id');
            $table->foreign('unit_id')->references('id')->on('unit')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tag')->onDelete('restrict');
            $table->primary(['unit_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag_unit');
    }
}
