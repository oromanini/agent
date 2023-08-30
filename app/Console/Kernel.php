<?php

namespace App\Console;

use App\Console\Commands\ExecuteEdeltecKitsImport;
use App\Jobs\ImportEdeltecKitsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new ImportEdeltecKitsJob())->cron('0 3 */3 * *');
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected $commands = [
        ExecuteEdeltecKitsImport::class,
    ];
}
