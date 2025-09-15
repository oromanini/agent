<?php

namespace App\Services;

use App\Models\Brand;
use App\Packages\SoolarApiPackage\Models\InverterBrand;
use App\Packages\SoolarApiPackage\Models\ModuleBrand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BrandService
{
    public function createBrand(array $validatedData): Model
    {
        $type = $validatedData['type'];
        unset($validatedData['type']);

        $lastEnum = $this->getLastEnum($type);

        $brand = DB::transaction(function () use ($validatedData, $type, $lastEnum) {
            $validatedData['brand_enum'] = $lastEnum + 1;
            $validatedData['type'] = $type;

            return Brand::create($validatedData);
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
        unset($validated['brand_enum']);
        $brand->update($validated);
    }

    public function delete($brand): void
    {
        $brand->delete();
    }
}
