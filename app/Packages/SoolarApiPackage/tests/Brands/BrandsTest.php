<?php

namespace App\Packages\SoolarApiPackage\tests\Brands;

use App\Models\User;
use App\Packages\SoolarApiPackage\Models\InverterBrand;
use App\Packages\SoolarApiPackage\Models\ModuleBrand;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BrandsTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
    }

    /** @test */
    public function testCreateModuleBrand_WhenValidDataIsProvided_ShouldCreateBrandAndStoreFiles(): void
    {
        $brandName = $this->faker->company;
        $brandData = [
            'brand' => $brandName,
            'warranty' => 25,
            'linear_warranty' => 30,
            'logo' => UploadedFile::fake()->image('logo.jpg'),
            'picture' => UploadedFile::fake()->image('picture.jpg'),
        ];

        $response = $this->postJson('/api/brands/module', $brandData);

        $response->assertStatus(201)
            ->assertJsonFragment(['brand' => strtoupper($brandName)]);

        $this->assertDatabaseHas('module_brands', [
            'brand' => strtoupper($brandName),
            'warranty' => 25,
            'linear_warranty' => 30,
        ], 'soollar');

        $brand = ModuleBrand::on('soollar')->first();
        $this->assertNotNull($brand->logo);
        $this->assertNotNull($brand->picture);
    }

    /** @test */
    public function testCreateInverterBrand_WhenValidDataIsProvided_ShouldCreateBrandInDatabase(): void
    {
        $brandName = $this->faker->company;
        $brandData = [
            'brand' => $brandName,
            'warranty' => 10,
            'overload' => 1.5,
        ];

        $response = $this->postJson('/api/brands/inverter', $brandData);

        $response->assertStatus(201)
            ->assertJsonFragment(['brand' => strtoupper($brandName)]);

        $this->assertDatabaseHas('inverter_brands', [
            'brand' => strtoupper($brandName),
            'warranty' => 10,
            'overload' => 1.5,
        ], 'soollar');
    }

    /** @test */
    public function testUpdateModuleBrand_WhenBrandExists_ShouldUpdateBrandInDatabase(): void
    {
        $brand = ModuleBrand::factory()->create();
        $newBrandName = $this->faker->company;

        $updateData = [
            'brand' => $newBrandName,
            'warranty' => 99,
            'linear_warranty' => 100,
        ];

        $response = $this->putJson("/api/brands/module/{$brand->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['brand' => strtoupper($newBrandName)]);

        $this->assertDatabaseHas('module_brands', [
            'id' => $brand->id,
            'brand' => strtoupper($newBrandName),
            'warranty' => 99,
            'linear_warranty' => 100,
        ], 'soollar');
    }

    /** @test */
    public function testUpdateInverterBrand_WhenBrandExists_ShouldUpdateBrandInDatabase(): void
    {
        $brand = InverterBrand::factory()->create();
        $newBrandName = $this->faker->company;

        $updateData = [
            'brand' => $newBrandName,
            'warranty' => 12,
            'overload' => 1.8,
        ];

        $response = $this->putJson("/api/brands/inverter/{$brand->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['brand' => strtoupper($newBrandName)]);

        $this->assertDatabaseHas('inverter_brands', [
            'id' => $brand->id,
            'brand' => strtoupper($newBrandName),
            'warranty' => 12,
            'overload' => 1.8,
        ], 'soollar');
    }

    /** @test */
    public function testDeleteModuleBrand_WhenBrandExists_ShouldSoftDeleteItFromDatabase(): void
    {
        $brand = ModuleBrand::factory()->create();

        $response = $this->deleteJson("/api/brands/module/{$brand->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('module_brands', [
            'id' => $brand->id
        ], 'soollar');
    }

    /** @test */
    public function testDeleteInverterBrand_WhenBrandExists_ShouldSoftDeleteItFromDatabase(): void
    {
        $brand = InverterBrand::factory()->create();

        $response = $this->deleteJson("/api/brands/inverter/{$brand->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('inverter_brands', [
            'id' => $brand->id
        ], 'soollar');
    }

    /** @test */
    public function testToggleBrandStatus_WhenBrandIsActive_ShouldSetItToInactive(): void
    {
        $brand = ModuleBrand::factory()->create(['active' => true]);

        $this->assertTrue($brand->active);

        $response = $this->patchJson("/api/brands/module/{$brand->id}/toggle");

        $response->assertStatus(200);

        $this->assertDatabaseHas('module_brands', [
            'id' => $brand->id,
            'active' => false,
        ], 'soollar');
    }

    /** @test */
    public function testCreateBrand_WhenNameAlreadyExists_ShouldReturnValidationError(): void
    {
        $existingBrandName = strtoupper($this->faker->company);
        ModuleBrand::factory()->create(['brand' => $existingBrandName]);

        $brandData = [
            'brand' => $existingBrandName,
            'warranty' => 10,
        ];

        $response = $this->postJson('/api/brands/module', $brandData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('brand');
    }
}
