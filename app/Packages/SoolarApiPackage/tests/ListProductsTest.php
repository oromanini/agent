<?php

namespace App\Packages\SoolarApiPackage\tests;

use App\Packages\SoolarApiPackage\SoolarApiService;
use Tests\TestCase;

class ListProductsTest extends TestCase
{
    public function testLogin_WithValidCredentials_ShouldReturnData(): void
    {
        $service = new SoolarApiService();

        dd($service->listProducts());
    }
}
