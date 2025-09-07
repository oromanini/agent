<?php

namespace App\Packages\SoolarApiPackage\tests;

use Tests\TestCase;
use App\Models\Kit;
use App\Packages\SoolarApiPackage\KitsManager;
use App\Packages\SoolarApiPackage\Models\Inverter;
use App\Packages\SoolarApiPackage\Models\InverterBrand;
use App\Packages\SoolarApiPackage\Models\Module;
use App\Packages\SoolarApiPackage\Models\ModuleBrand;
use App\Packages\SoolarApiPackage\Models\SoollarImportHistory;
use App\Packages\SoolarApiPackage\Models\Structure;
use App\Packages\SoolarApiPackage\Models\Connector;
use App\Packages\SoolarApiPackage\Repositories\SoollarApiRepository;
use App\Packages\SoolarApiPackage\Services\CableService;
use Mockery;

class KitsManagerTest extends TestCase
{
    private $repositoryMock;
    private $cableServiceMock;
    private $kitsManager;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testKitsManagerHandleCreatesKitsWithMultipleInverters(): void
    {
        $this->repositoryMock = Mockery::mock(SoollarApiRepository::class);
        $this->cableServiceMock = Mockery::mock(CableService::class);

        $historyMock = Mockery::mock('alias:' . SoollarImportHistory::class);
        $historyMock->shouldReceive('updateProcess')->andReturnNull()->twice();

        $this->kitsManager = Mockery::mock(KitsManager::class, [$this->repositoryMock, $this->cableServiceMock])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $saveOrUpdateCallCount = 0;
        $this->kitsManager->shouldReceive('saveOrUpdateKit')
            ->with(Mockery::on(function ($kit) {
                return $kit instanceof Kit && !empty($kit->description);
            }))
            ->andReturnUsing(function () use (&$saveOrUpdateCallCount) {
                $saveOrUpdateCallCount++;
            });

        $moduleBrand = Mockery::mock(ModuleBrand::class)->shouldIgnoreMissing();
        $moduleBrand->shouldReceive('getAttribute')->with('brand')->andReturn('RENEPV');
        $moduleBrand->shouldReceive('getAttribute')->with('active')->andReturn(true);

        $inverterBrand = Mockery::mock(InverterBrand::class)->shouldIgnoreMissing();
        $inverterBrand->shouldReceive('getAttribute')->with('brand')->andReturn('SAJ');
        $inverterBrand->shouldReceive('getAttribute')->with('active')->andReturn(true);
        $inverterBrand->shouldReceive('getAttribute')->with('overload')->andReturn(50);

        $module = Mockery::mock(Module::class)->shouldIgnoreMissing();
        $module->shouldReceive('getAttribute')->with('power')->andReturn(550);
        $module->shouldReceive('getAttribute')->with('price')->andReturn(200);
        $module->shouldReceive('getAttribute')->with('brand')->andReturn('RENEPV');

        $inverter = Mockery::mock(Inverter::class)->shouldIgnoreMissing();
        $inverter->shouldReceive('getAttribute')->with('power')->andReturn(10);
        $inverter->shouldReceive('getAttribute')->with('price')->andReturn(500);
        $inverter->shouldReceive('getAttribute')->with('brand')->andReturn('SAJ');
        $inverter->shouldReceive('getAttribute')->with('voltage')->andReturn('220V');

        $structure = Mockery::mock(Structure::class)->shouldIgnoreMissing();
        $structure->shouldReceive('getAttribute')->with('name')->andReturn('Cerâmica');
        $structure->shouldReceive('getAttribute')->with('price')->andReturn(100);

        $connector = Mockery::mock(Connector::class)->shouldIgnoreMissing();
        $connector->shouldReceive('getAttribute')->with('name')->andReturn('MC4');
        $connector->shouldReceive('getAttribute')->with('price')->andReturn(50);

        $this->repositoryMock->shouldReceive('deactivateAllKits')->once();
        $this->repositoryMock->shouldReceive('getModuleBrands')->andReturn([$moduleBrand])->once();
        $this->repositoryMock->shouldReceive('getInverterBrands')->andReturn([$inverterBrand])->once();
        $this->repositoryMock->shouldReceive('findModulesByBrand')->with($moduleBrand)->andReturn([$module])->once();
        $this->repositoryMock->shouldReceive('findInvertersByBrand')->with($inverterBrand)->andReturn([$inverter])->once();
        $this->repositoryMock->shouldReceive('getStructureByModelName')->andReturn($structure);
        $this->repositoryMock->shouldReceive('getConnectors')->andReturn($connector);
        $this->cableServiceMock->shouldReceive('getBestCableOption')->andReturn(['cost' => 10, 'description' => ['Cabo 6mm 10m'], 'quantity' => 10]);

        $this->kitsManager->handle();

        $this->assertEquals(238, $saveOrUpdateCallCount, "O método saveOrUpdateKit foi chamado um número incorreto de vezes.");
    }
}

