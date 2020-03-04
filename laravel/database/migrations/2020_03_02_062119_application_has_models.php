<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class ApplicationHasModels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_has_models', function (Blueprint $table) {
            $table->increments('id')->index()->unsigned();
            $table->unsignedInteger('app_id');
            $table->foreign('app_id')->references('id')->on('applications')->onDelete('cascade');
            $table->enum('model_type', ['level', 'unit', 'topic', 'lesson'])->default('lesson');
            $table->unsignedInteger('model_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_has_models');
    }
}
