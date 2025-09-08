<?php

namespace App\Services;

use App\Models\Brand;
use App\Packages\SoolarApiPackage\Models\InverterBrand;
use App\Packages\SoolarApiPackage\Models\ModuleBrand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BrandService
{
    public function createBrand(array $validatedData, string $type): Model
    {
        $model = $this->getModel($type);
        $brand = new Brand();

            DB::transaction(function () use ($model, $validatedData, $type, &$brand) {

            $newBrand = $model->create($validatedData);

            $brand = Brand::create([
                'name' => $newBrand->brand,
                'type' => $type,
                'brand_enum' => $this->getLastEnum($type) + 1,
            ]);
        });

        return $brand;
    }

    private function getModel(string $type): InverterBrand|ModuleBrand
    {
        return match ($type) {
            'panel' => new ModuleBrand(),
            'inverter' => new InverterBrand(),
            default => throw new \InvalidArgumentException('Invalid brand type.'),
        };
    }

    private function getLastEnum(string $type): int
    {
        return (int) Brand::where('type', $type)->max('brand_enum');
    }

    public function update(Brand $brand, array $validated): void
    {
        $brand->update($validated);
    }

    public function delete($brand): void
    {
        $brand->delete();
    }
}
