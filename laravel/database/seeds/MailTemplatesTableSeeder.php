<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MailTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App\MailTemplate::where('mail_type', 'App\Mail\ClassInviteMail')->count() < 1) {
            DB::table('mail_templates')->insert([
                'mail_type' => 'App\Mail\ClassInviteMail',
                'subject' => 'Class invitation',
                'body' => 'Hello, {{ $studentFirstName }} {{ $studentLastName }}.<br/>{{ $teacherFirstName }} {{ $teacherLastName }} has invited you to join the {{ $className }} class.<br/><br/>Best regards, Health Numeracy Project team.'
            ]);
        }
    }
}
