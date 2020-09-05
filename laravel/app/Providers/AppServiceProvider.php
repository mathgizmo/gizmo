<?php

namespace App\Providers;

use App\ClassOfStudents;
use App\Observers\ClassObserver;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('recaptcha', 'App\\Validators\\ReCaptcha@validate');
        ClassOfStudents::observe(ClassObserver::class);
        /** DB log */
        /* DB::listen(function ($query) {
            $log = ['QUERY' => $query->sql, 'TIME' => $query->time];
            $dbLog = new Logger('DB');
            $dbLog->pushHandler(new StreamHandler(storage_path('logs/DB-'.Carbon::now()->toDateString().'.log')));
            $dbLog->info('DBLog', $log);
        }); */
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
