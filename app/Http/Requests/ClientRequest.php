<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
        const MALE = 'é obrigatório';
        const FEMALE = 'é obrigatória';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'type' => 'required',
            'phone_number' => 'required',
            'street' => 'required',
            'document' => 'required',
            'address_number' => 'required',
            'neighborhood' => 'required',
            'state' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome ' . self::MALE,
            'type.required' => 'O tipo ' . self::MALE,
            'phone_number.required' => 'O telefone ' . self::MALE,
            'street.required' => 'A rua ' . self::FEMALE,
            'address_number.required' => 'O número ' . self::MALE,
            'neighborhood.required' => 'O bairro ' . self::MALE,
            'state.required' => 'O Estado ' . self::MALE,
        ];
    }
}
