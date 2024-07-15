<?php

namespace App\Providers;

use App\Events\AfterSaleProcessDepartmentChanged;
use App\Listeners\UpdateChecklistAndStatus;
use App\Models\Contract;
use App\Models\Financing;
use App\Models\Inspection;
use App\Models\Installation;
use App\Observers\ContractObserver;
use App\Observers\FinancingObserver;
use App\Observers\InspectionObserver;
use App\Observers\InstallationObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        AfterSaleProcessDepartmentChanged::class => [
            UpdateChecklistAndStatus::class
        ]
    ];

    public function boot()
    {
        Inspection::observe(InspectionObserver::class);
        Financing::observe(FinancingObserver::class);
        Contract::observe(ContractObserver::class);
    }
}
