<?php

namespace App\Helpers;

use App\Enums\InverterBrands;
use App\Enums\PanelBrands;
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    const PANEL = "panel";

    public function setImageByBrand(string $type, string|int $brand): string
    {
        $brand = $this->setBrandString($type, $brand);

        $validExtensions = ['png'];
        $imagesList = [];
        $directory = $this->getDirectory($type);

        if (is_dir($directory)) {
            $images = scandir($directory);

            foreach ($images as $image) {
                if ($image !== '.' && $image !== '..') {
                    $extension = pathinfo($image, PATHINFO_EXTENSION);

                    if (in_array(strtolower($extension), $validExtensions)) {
                        $withoutExtension = pathinfo($image, PATHINFO_FILENAME);
                        $imagesList[$withoutExtension] = $directory . '/' . $image;
                    }
                }
            }
        }

        if (!array_key_exists($brand, $imagesList)) {
            throw new \Exception("image {$brand} not found!");
        }

        return $imagesList[strtolower($brand)];
    }

    private function getDirectory(string $type): string
    {
        return match ($type) {
            'panel' => public_path('img/panels'),
            'inverter' => public_path('img/inverters'),
            default => throw new \Exception('brand not found')
        };
    }

    private function setBrandString(string $type, int|string $brand)
    {
        if (!is_numeric($brand)) {
            return strtolower($brand);
        }

        return $type == self::PANEL
            ? strtolower(PanelBrands::tryFrom($brand)->name)
            : strtolower(InverterBrands::tryFrom($brand)->name);
    }
}
