<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Setting;
class UpdateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function ($table) {
            $table->string('key')->unique()->change();
        });
        Schema::table('settings', function ($table) {
            $table->text('value')->change();
        });
        DB::table('settings')->insert([
        	'key' => 'Home1',
            'label' => 'Welcome Title',
            'value' => 'Welcome to Health Numeracy Learning Object!'
        ]);
        DB::table('settings')->insert([
            'key' => 'Home2',
            'label' => 'Welcome Subtitle',
            'value' => 'Improve your numeracy skills. Sharpen your reasoning. Learn something new.'
        ]);
        DB::table('settings')->insert([
            'key' => 'Home3',
            'label' => 'Welcome Introduction',
            'value' => 'Once you log in, you will see all topics, organized into units and levels. Each topic contains a sequence of lessons. To complete a lesson, you need to correctly answer several questions (this number can be changed in your settings). There are different ways to advance through the material. You can take a placement test and skip some topics. Or, once inside a topic, you can test out of it and move to the next topic. You can always go back to any topic or any lesson you have completed, to review it.'
        ]);
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function ($table) {
            $table->string('key')->change();
        });
        Schema::table('settings', function ($table) {
            $table->string('value')->change();
        });
        Setting::where('key', 'Home1')->first()->delete();
        Setting::where('key', 'Home2')->first()->delete();
        Setting::where('key', 'Home3')->first()->delete();
    }
}