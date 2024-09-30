<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    public function setImageByBrand(string $type, string $brand): string
    {
        $brand = strtolower($brand);
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

        return $imagesList[$brand];
    }

    private function getDirectory(string $type)
    {
        return match ($type) {
            'panel' => public_path('EdeltecApiPackage/img/panels'),
            'inverter' => public_path('EdeltecApiPackage/img/inverter_picture'),
            default => throw new \Exception('brand not found')
        };
    }
}
