<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateStudentsTable12 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (\App\Student::whereNull('first_name')->orWhereNull('last_name')->get() as $student) {
            if (!$student->first_name) {
                $student->first_name = $student->name;
            } else if (!$student->last_name) {
                $student->last_name = $student->name;
            }
            $student->save();
        }

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        DB::table('mail_templates')->where('mail_type', 'App\Mail\ClassInviteMail')->update([
            'body' => '<p><em>Hello&nbsp;{{ $studentFirstName }} {{ $studentLastName }}</em>,<br /><strong>{{ $teacherFirstName }} {{ $teacherLastName }} has invited you to join the {{ $className }} class.</strong></p><p>To access the classroom please go to <strong><a href="https://bit.ly/henupr">bit.ly/henupr</a></strong>, and if need be register into the web-application.</p><p>If you believe you have received this email as spam please forward this email with a note to our technical team at <a href="mailto:healthnumeracyproject@gmail.com">healthnumeracyproject@gmail.com</a></p><p>Otherwise enjoy the experience.</p><p><em>Taras and Miroslav for the&nbsp;Health Numeracy Project team.</em></p>'
        ]);

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
