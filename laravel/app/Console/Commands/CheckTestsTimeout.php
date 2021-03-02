<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckTestsTimeout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:check-test-timeout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check tests that is timed out';

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
        $this->info(app('\App\Http\AdminControllers\JobController')->checkTestsTimeout());
    }
}
