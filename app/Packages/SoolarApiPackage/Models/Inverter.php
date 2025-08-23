<?php

namespace App\Packages\SoolarApiPackage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inverter extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'soollar';

    protected $guarded = [];

    public function getImage(): ?array
    {
        $inverterBrand = InverterBrand::query()
            ->where('brand', $this->brand)
            ->first();

        if (!$inverterBrand) {
            return null;
        }

        return [
            "logo" => $inverterBrand->logo,
            "picture" => $inverterBrand->picture
        ];
    }
}
