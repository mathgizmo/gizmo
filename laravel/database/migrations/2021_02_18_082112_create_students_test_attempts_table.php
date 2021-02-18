<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTestAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students_test_attempts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('attempt_no')->default(1);
            $table->unsignedInteger('test_student_id');
            $table->unsignedInteger('questions_count')->nullable(true);
            $table->double('mark')->nullable(true);
            $table->timestamp('start_at')->nullable(true);
            $table->timestamp('end_at')->nullable(true);

            $table->foreign('test_student_id')->references('id')->on('classes_applications_students')->onDelete('cascade');
        });

        Schema::table('students_test_questions', function (Blueprint $table) {
            $table->unsignedInteger('class_app_id')->nullable()->change();
            $table->unsignedInteger('attempt_id')->after('class_app_id');
            $table->foreign('attempt_id')->references('id')->on('students_test_attempts')->onDelete('cascade');
        });

        Schema::table('classes_applications_students', function (Blueprint $table) {
            $table->unsignedInteger('attempts_count')->default(0)->after('is_revealed');
            $table->unsignedInteger('resets_count')->default(0)->after('attempts_count');
        });

        $rows = DB::table('classes_applications_students')
            ->whereNotNull('questions_count')->orWhereNotNull('mark')->orWhereNotNull('start_at')
            ->get();
        foreach ($rows as $row) {
            $attempt_id = DB::table('students_test_attempts')->insertGetId([
                'test_student_id' => $row->id,
                'questions_count' => $row->questions_count,
                'mark' => $row->mark,
                'start_at' => $row->start_at,
                'end_at' => $row->end_at
            ]);
            DB::table('students_test_questions')
                ->where('student_id', $row->student_id)
                ->where('class_app_id', $row->class_app_id)
                ->update([
                    'attempt_id' => $attempt_id
                ]);
            DB::table('classes_applications_students')
                ->where('id', $row->id)
                ->update([
                    'attempts_count' => 1
                ]);
        }

        Schema::table('classes_applications_students', function (Blueprint $table) {
            $table->dropColumn('questions_count');
            $table->dropColumn('mark');
            $table->dropColumn('start_at');
            $table->dropColumn('end_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students_test_attempts');
    }
}
