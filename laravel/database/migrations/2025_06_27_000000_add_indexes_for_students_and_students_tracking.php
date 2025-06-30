<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesForStudentsAndStudentsTracking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->index('first_name');
            $table->index('last_name');
            $table->index('email');
            $table->index('is_super');
            $table->index('is_teacher');
            $table->index('is_researcher');
            $table->index('is_self_study');
            $table->index('is_admin');
            $table->index('is_registered');
            $table->index('country_id');
            $table->index('created_at');
            $table->index('id');
            $table->index(['first_name', 'last_name']);
            $table->index(['email', 'is_teacher']);
            $table->index(['country_id', 'is_teacher']);
        });

        Schema::table('students_tracking', function (Blueprint $table) {
            $table->index(['student_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['first_name']);
            $table->dropIndex(['last_name']);
            $table->dropIndex(['email']);
            $table->dropIndex(['is_super']);
            $table->dropIndex(['is_teacher']);
            $table->dropIndex(['is_researcher']);
            $table->dropIndex(['is_self_study']);
            $table->dropIndex(['is_admin']);
            $table->dropIndex(['is_registered']);
            $table->dropIndex(['country_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['id']);
            $table->dropIndex(['first_name', 'last_name']);
            $table->dropIndex(['email', 'is_teacher']);
            $table->dropIndex(['country_id', 'is_teacher']);
        });

        Schema::table('students_tracking', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'id']);
        });
    }
}
