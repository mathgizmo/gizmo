<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->unsignedInteger('teacher_id')->nullable();
            // $table->date('due_date')->nullable();
        });

        // create default class
        $class = new \App\ClassOfStudents();
        $class->name = 'Default Class';
        $class->save();
        foreach (\App\Application::all() as $app) {
            DB::table('classes_applications')->insert([
                'class_id' => $class->id,
                'app_id' => $app->id
            ]);
        }
        foreach (\App\Student::all() as $student) {
            DB::table('classes_students')->insert([
                'class_id' => $class->id,
                'student_id' => $student->id
            ]);
        }
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
