<?php

namespace App\Helpers;

use App\Enums\InverterBrands;
use App\Enums\PanelBrands;
use App\Packages\SoolarApiPackage\Models\InverterBrand;
use App\Packages\SoolarApiPackage\Models\ModuleBrand;
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    const PANEL = "panel";
    const INVERTER = "inverter";

    public function setImageByBrand(string $type, string|int $brand, ?string $distributor = null): string
    {
        if ($distributor === 'soollar') {
            $brandName = $this->setBrandString($type, $brand);
            $model = $this->getModelInstance($type);

            $brandRecord = $model->where('brand', 'LIKE', $brandName)->first();

            if ($brandRecord && $brandRecord->logo) {
                return Storage::url($brandRecord->logo);
            }

            throw new \Exception("Imagem para a marca '{$brandName}' do distribuidor 'soollar' não encontrada no banco de dados.");
        }

        $brandName = $this->setBrandString($type, $brand);
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
                        $imagesList[strtolower($withoutExtension)] = $directory . '/' . $image;
                    }
                }
            }
        }

        if (!array_key_exists($brandName, $imagesList)) {
            throw new \Exception("Imagem estática '{$brandName}.png' não encontrada!");
        }

        return $imagesList[$brandName];
    }

    private function getModelInstance(string $type): ModuleBrand|InverterBrand
    {
        return match ($type) {
            self::PANEL => new ModuleBrand(),
            self::INVERTER => new InverterBrand(),
            default => throw new \Exception('Tipo de modelo inválido: ' . $type)
        };
    }

    private function getDirectory(string $type): string
    {
        return match ($type) {
            self::PANEL => public_path('img/panels'),
            self::INVERTER => public_path('img/inverters'),
            default => throw new \Exception('Tipo de diretório não encontrado: ' . $type)
        };
    }

    private function setBrandString(string $type, int|string $brand): string
    {
        if (!is_numeric($brand)) {
            return strtolower($brand);
        }

        return $type == self::PANEL
            ? strtolower(PanelBrands::tryFrom($brand)->name)
            : strtolower(InverterBrands::tryFrom($brand)->name);
    }
}
