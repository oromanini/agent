<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client' => 'required',
            'average_consumption' => 'required',
            'kw_price' => 'required',
            'tension_pattern' => 'required',
            'installation_address' => 'required',
            'roof_structure' => 'required',
            'orientation' => 'required',
            'kit_id' => 'required',
            'agent' => ''
        ];
    }

    public function messages(): array
    {
        $male = 'é obrigatório';
        $female = 'é obrigatória';

        return [
            'client.required' => 'O cliente ' . $male,
            'average_consumption.required' => 'O consumo ' . $male,
            'kw_price.required' => 'O preço do kWh ' . $male,
            'tension_pattern.required' => 'A tensão do cliente ' . $female,
            'installation_address.required' => 'O endereço de instalaçao ' . $male,
            'roof_structure.required' => 'O telhado ' . $male,
            'orientation.required' => 'A orientação ' . $female,
            'kit_id.required' => 'O kit ' . $male,
        ];
    }
}
