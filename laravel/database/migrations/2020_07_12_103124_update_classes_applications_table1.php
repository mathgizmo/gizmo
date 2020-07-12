<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateClassesApplicationsTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classes_applications', function (Blueprint $table) {
            $table->timestamp('start_at')->nullable(true);
            $table->boolean('time_to_due_date')->default(false);
        });

        // remove in_dev models from assignments
        foreach (DB::table('application_has_models')->get() as $row) {
            if ($row) {
                switch ($row->model_type) {
                    default:
                    case 'level':
                        $entity = \App\Level::where('id', $row->model_id)->first();
                        break;
                    case 'unit':
                        $entity = \App\Unit::where('id', $row->model_id)->first();
                        break;
                    case 'topic':
                        $entity = \App\Topic::where('id', $row->model_id)->first();
                        break;
                    case 'lesson':
                        $entity = \App\Lesson::where('id', $row->model_id)->first();
                        break;
                }
                if ($entity && $entity->dev_mode) {
                    DB::table('application_has_models')
                        ->where('model_type', $row->model_type)
                        ->where('model_id', $row->model_id)->delete();
                }
            }
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
