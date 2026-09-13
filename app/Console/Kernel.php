<?php

declare(strict_types = 1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

// @codeCoverageIgnoreStart
final class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('auth:clear-resets')->hourly();

        $schedule->command('clean:logs')->quarterly();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
// @codeCoverageIgnoreEnd
