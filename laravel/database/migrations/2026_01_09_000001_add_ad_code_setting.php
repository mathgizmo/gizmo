<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAdCodeSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')->insert([
            [
                'key' => 'ad_code',
                'label' => 'Advertisement Code (AdSense, etc.)',
                'value' => '',
                'type' => 'text'
            ],
            [
                'key' => 'ad_message',
                'label' => 'Advertisement Message',
                'value' => 'Math Gizmo is a free educational platform that is not funded by your institution. To help cover our operating costs and keep this resource available to everyone, we display advertisements. If you would like to support us and enjoy an ad-free experience, please consider making a donation.',
                'type' => 'text'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')->whereIn('key', ['ad_code', 'ad_message'])->delete();
    }
}
