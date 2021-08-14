<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateSettingsTable5 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $val = 'The developers of the <strong>HNP webapp</strong> with support from the <a href="https://heqco.ca/" target="_blank"><strong>Higher Education Quality Council of Ontario</strong></a> along with <a href="https://www.georgebrown.ca/" target="_blank"><strong>George Brown College</strong></a> and <a href="https://www.mcmaster.ca/" target="_blank"><strong>McMaster university</strong></a> are inviting you to help evaluate it as a useful educational tool for health numeracy.
        <br/>
        Your teacher has integrated the HNP webapp into your course materials and you will have access to the application whether you participate or not, thus participation is voluntary. Your teacher will not know whether you will participate or not, and we aim to evaluate the application not the research participants.
        <br/>
        We would like to invite you to take part in one or more of 4 formats in which we will be using to evaluate the app. They are described at length in the letter of informed consent that you need to read. You can have access to it here. Three of them involve compensation to offset time and any costs you incur, and one does not require you to do anything except provide access to data. lease read carefully
        <br/>
        To get more information about the study please email <strong>Taras Gula</strong> at <a href="mailto:tgula@georgebrown.ca">tgula@georgebrown.ca</a> or <strong>Miroslav Lovric</strong> at <a href="mailto:lovric@mcmaster.ca">lovric@mcmaster.ca</a>.';
        DB::table('settings')->insert([
            'key' => 'research_status',
            'label' => 'Research Status Message',
            'value' => $val,
            'type' => 'text'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')
            ->where('key', 'research_status')
            ->delete();
    }
}
