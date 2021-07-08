<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UpdateClassesTable2 extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classes', function (Blueprint $table) {
            DB::statement("ALTER TABLE `classes` MODIFY COLUMN `subscription_type` enum('open','assigned','invitation','closed') NOT NULL DEFAULT 'open';");
            $table->uuid('key')->unique()->nullable()->after('teacher_id');
        });

        DB::table('classes')->where('subscription_type', 'invitation')->update(['subscription_type' => 'assigned']);
        foreach (\App\ClassOfStudents::get() as $class) {
            $class->key = (string) Str::uuid();
            $class->save();
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
