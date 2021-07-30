<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateClassesStudentsTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classes_students', function (Blueprint $table) {
            $table->boolean('is_consent_read')->default(false);
            $table->boolean('is_element1_accepted')->default(false);
            $table->boolean('is_element2_accepted')->default(false);
            $table->boolean('is_element3_accepted')->default(false);
            $table->boolean('is_element4_accepted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classes_students', function (Blueprint $table) {
            $table->dropColumn('is_consent_read');
            $table->dropColumn('is_element1_accepted');
            $table->dropColumn('is_element2_accepted');
            $table->dropColumn('is_element3_accepted');
            $table->dropColumn('is_element4_accepted');
        });
    }
}
