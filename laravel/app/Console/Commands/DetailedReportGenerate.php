<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DetailedReportGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:generate {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate reports';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get report name
        $reportname = $this->argument('name');
        switch( $reportname ) {
            case 'detailed-report':
                $request = new \Illuminate\Http\Request();
                $this->info(app('\App\Http\Controllers\JobController')->generateClassDetailedReports($request));
                break;
            default:
                $this->info('report "'.$reportname.'" does not exists');
        }
    }
}
