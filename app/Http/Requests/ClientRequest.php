<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'type' => 'required',
            'document' => 'required',
            'phone_number' => 'required',
            'street' => 'required',
            'address_number' => 'required',
            'neighborhood' => 'required',
            'state' => 'required',
        ];
    }

    public function messages(): array
    {
        $male = 'é obrigatório';
        $female = 'é obrigatória';

        return [
            'name.required' => 'O nome ' . $male,
            'type.required' => 'O tipo ' . $male,
            'document.required' => 'O documento ' . $male,
            'phone_number.required' => 'O telefone ' . $male,
            'street.required' => 'A rua ' . $female,
            'address_number.required' => 'O número ' . $male,
            'neighborhood.required' => 'O bairro ' . $male,
            'state.required' => 'O Estado ' . $male,
        ];
    }
}
