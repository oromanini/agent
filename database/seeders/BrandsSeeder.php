<?php

namespace Database\Seeders;

use App\Enums\InverterBrands;
use App\Enums\PanelBrands;
use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandsSeeder extends Seeder
{
    public function run()
    {
        foreach (PanelBrands::cases() as $panelBrand) {
            Brand::firstOrCreate(
                [
                    'name' => $panelBrand->name,
                    'type' => 'panel',
                    'brand_enum' => $panelBrand->value,
                ],
            );
        }

        foreach (InverterBrands::cases() as $inverterBrand) {
            Brand::firstOrCreate(
                [
                    'name' => $inverterBrand->name,
                    'type' => 'inverter',
                    'brand_enum' => $inverterBrand->value,

                ],
            );
        }
    }
}

