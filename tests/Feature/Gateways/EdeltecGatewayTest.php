<?php

namespace Tests\Feature\Gateways;

use App\Jobs\ImportEdeltecKitsJob;
use App\Packages\EdeltecApiPackage\EdeltecApiService;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EdeltecGatewayTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->edeltecApiService = new EdeltecApiService(client: new Client());
        $this->apiToken = EdeltecApiService::setApiToken();
    }

    public function testImportEdeltecKitsJob_WithValidParameters_ShouldQueueJob(): void
    {
        Queue::fake();
        ImportEdeltecKitsJob::dispatch();
        Queue::assertPushed(ImportEdeltecKitsJob::class);
    }
}
