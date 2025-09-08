<?php

namespace App\Helpers;

use App\Models\Brand;

class ImageHelper
{
    public const PANEL = "panel";
    public const INVERTER = "inverter";
    public const LOGO = 'logo';
    public const PICTURE = 'picture';
    public const EXTENSION = '.png';

    public function setImageByBrand(string $type, string|int $brand, string $imageType): string
    {
        $directory = $this->getDirectory($type, $imageType);
        $brandName = $this->setBrandString($type, $brand);

        return $directory . $brandName . self::EXTENSION;
    }

    private function getDirectory(string $type, string $imageType): string
    {
        $panel_logos = '/storage/module_brand_logos/';
        $panel_pictures = '/storage/module_brand_pictures/';
        $inverter_logos = '/storage/inverter_brand_logos/';
        $inverter_pictures = '/storage/inverter_brand_pictures/';

        $directory = null;

        ($type == self::PANEL && $imageType == self::LOGO) && $directory = $panel_logos;
        ($type == self::PANEL && $imageType == self::PICTURE) && $directory = $panel_pictures;
        ($type == self::INVERTER && $imageType == self::LOGO) && $directory = $inverter_logos;
        ($type == self::INVERTER && $imageType == self::PICTURE) && $directory = $inverter_pictures;

        return $directory;
    }

    private function setBrandString(string $type, int|string $brand): string
    {
        $brand = Brand::query()
            ->where(function ($query) use ($brand) {
                is_integer($brand)
                    ? $query->where('brand_enum', $brand)
                    : $query->where('brand', 'LIKE', "%{$brand}%");
            })
            ->where('type', $type)
            ->first();

        is_null($brand) && throw new \Exception('Marca inexistente!');

        return $brand->name;
    }
}
