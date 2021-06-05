<?php

namespace App\Console;

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->monthly();
        $schedule->call('App\Http\Controllers\UserController@reset')->monthly();
        $schedule->call('App\Http\Controllers\UserController@MonthlyExpence')->monthly();
        $schedule->call('App\Http\Controllers\UserController@MonthlyIncome')->monthly();
        $schedule->call('App\Http\Controllers\UserController@DailyExpence')->daily();
        $schedule->call('App\Http\Controllers\UserController@DailyIncome')->daily();
        $schedule->call('App\Http\Controllers\UserController@WeekyExpence')->weekly();
        $schedule->call('App\Http\Controllers\UserController@WeeklyIncome')->weekly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
