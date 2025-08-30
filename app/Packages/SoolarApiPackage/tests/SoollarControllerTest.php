<?php

namespace App\Packages\SoolarApiPackage\tests;

use App\Http\Controllers\SoollarController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Jobs\SoollarKitsUpdateJob;
use App\Jobs\SoollarProductsUpdateJob;
use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use App\Packages\SoolarApiPackage\Models\SoollarImportHistory;
use App\Packages\SoolarApiPackage\Repositories\SoollarApiRepository;
use App\Packages\SoolarApiPackage\Services\CableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class SoollarControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testController_IfDispatchesSoollarProductsAndKitsUpdate_ShouldEnqueueJobs(): void
    {
        Queue::fake();

        $history = $this->mock(SoollarImportHistory::class)
            ->shouldAllowMockingProtectedMethods();
        $history->shouldReceive('isAnotherProcessRunning')
            ->andReturnFalse();

        $response = $this->withoutMiddleware()
            ->post(route('soollar.update'));

        Queue::assertPushed(SoollarProductsUpdateJob::class, function ($job) {
            $chainedJobs = collect($job->chained)->map(fn ($serialized) => unserialize($serialized));
            return $chainedJobs->contains(fn ($chainedJob) => $chainedJob instanceof \App\Jobs\SoollarKitsUpdateJob);
        });
    }
}
