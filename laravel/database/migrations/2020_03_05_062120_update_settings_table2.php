<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Setting;

class UpdateSettingsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')->insert([
            'key' => 'Home4',
            'label' => 'Welcome Introduction Title',
            'value' => 'How to use this learning tool'
        ]);
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::where('key', 'Home4')->first()->delete();
    }
}