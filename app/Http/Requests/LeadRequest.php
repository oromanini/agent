<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lead_name' =>  ['required', 'string'],
            'average_consumption' =>  ['required', 'numeric'],
            'kwh_price' =>  ['required', 'numeric'],
            'tension_pattern' =>  ['required', 'numeric'],
            'phone_number' =>  ['required', 'string'],
            'state' =>  ['required', 'string'],
            'city' =>  ['required', 'string'],
            'roof_structure' =>  ['required', 'numeric'],
            'kit_id' =>  ['required', 'string'],
        ];
    }
}
