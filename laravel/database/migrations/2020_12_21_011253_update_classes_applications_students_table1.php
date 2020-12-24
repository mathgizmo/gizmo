<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateClassesApplicationsStudentsTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classes_applications_students', function (Blueprint $table) {
            $table->unsignedInteger('questions_count')->nullable(true);
            $table->double('mark')->nullable(true);
            $table->timestamp('start_at')->nullable(true);
            $table->timestamp('end_at')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
