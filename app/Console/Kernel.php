<?php

namespace App\Console;

use App\Console\Commands\testArmor;
use Aws\Command;
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
        Commands\processDBLBundle::class,
	    Commands\testArmor::class,
	    Commands\countSophia::class,
	    Commands\organizations_dbl_status::class,
	    Commands\filesystem_update::class,
	    Commands\checkIDs::class,
	    Commands\fetch_s3_audio_length::class,
	    Commands\fetch_fcbh_apk::class,
	    Commands\fetch_beblia_bible::class,
	    Commands\fetchAlphabets::class,
	    Commands\fetchLanguageDescriptions::class,
	    Commands\generate_worldFactbook::class,
	    Commands\compare_ebible::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(testArmor::class)->weekly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
