<?php

namespace App\Packages\SoolarApiPackage\tests;


use App\Jobs\SoollarProductsUpdateJob;
use App\Packages\SoolarApiPackage\Models\SoollarImportHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
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
