<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLessonTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('lesson', 'challenge')) {
            Schema::table('lesson', function ($table) {
                $table->boolean('challenge')->default(false);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('lesson', 'challenge')) {
            Schema::table('lesson', function ($table) {
                $table->dropColumn('challenge');
            });
        }
    }
}
