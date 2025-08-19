<?php

namespace App\Http\Requests;

use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class SoollarProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'products_category' => ['required', new Enum(ProductCategoriesEnum::class)],
            'warehouse' => ['required', new Enum(WarehouseEnum::class)],
        ];
    }
}
