<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Setting;

class UpdateSettingsTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')->insert([
            'key' => 'topic_testout_max_questions_num',
            'label' => 'Max number of questions per topic test out',
            'value' => '5'
        ]);
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::where('key', 'topic_testout_max_questions_num')->first()->delete();
    }
}