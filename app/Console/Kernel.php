<?php

namespace App\Console;

use App\Console\Commands\ExecuteEdeltecKitsImport;
use App\Console\Commands\ExecuteFotusKitsImport;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected $commands = [
        ExecuteEdeltecKitsImport::class,
        ExecuteFotusKitsImport::class,
    ];
}
