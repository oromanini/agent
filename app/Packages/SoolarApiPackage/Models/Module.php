<?php

namespace App\Packages\SoolarApiPackage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'soollar';

    protected $guarded = [];

    public function getImage(): ?array
    {
        $moduleBrand = ModuleBrand::query()
            ->where('brand', $this->brand)
            ->first();

        if (!$moduleBrand) {
            return null;
        }

        return [
            "logo" => $moduleBrand->logo,
            "picture" => $moduleBrand->picture
        ];
    }
}
