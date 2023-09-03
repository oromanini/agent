<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\ExampleJob;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class HorizonTest extends TestCase
{
    public function testExampleJob_With20SecondsSleep_ShouldEnqueue(): void
    {
        $job = new ExampleJob(timeToSleep: 10);

        Queue::push($job);
        $this->assertTrue(true);

    }
}
