<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkCostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'classification' => 'required|integer|unique:work_costs,classification,' . $this->route('work_cost')?->id,
            'costs' => 'required|json',
        ];
    }
}
