<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantTagPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participant_tag', function (Blueprint $table) {
            $table->unsignedInteger('participant_id');
            $table->unsignedInteger('tag_id');
            $table->foreign('participant_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tag')->onDelete('restrict');
            $table->primary(['participant_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('participant_tag');
    }
}
