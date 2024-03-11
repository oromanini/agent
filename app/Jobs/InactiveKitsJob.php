<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InactiveKitsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Collection $kits)
    {}

    public function handle(): void
    {
        DB::transaction(function () {
            foreach ($this->kits as $kit) {
                $kit->is_active = false;
                $kit->update();
            }
        });

        Log::info('Todos os kits foram inativados, iniciando integração...');
    }
}
