<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgentRequest extends FormRequest
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
            'email' => 'required',
            'phone_number' => 'required',
            'ascendant' => 'required',
            'cpf' => 'required',
//            'cnpj' => 'required',
            'city' => 'required',
            'password' => 'required',
        ];
    }

    public function messages(): array
    {
        $male = 'é obrigatório';
        $female = 'é obrigatória';

        return [
            'name.required' => 'O nome ' . $male,
            'email.required' => 'O email ' . $male,
            'phone_number.required' => 'O telefone ' . $male,
            'ascendant.required' => 'O ascendente ' . $female,
            'cpf.required' => 'O CPF ' . $male,
            'cnpj.required' => 'O CNPJ ' . $male,
            'city.required' => 'A Cidade ' . $female,
            'password.required' => 'A senha ' . $female,
        ];
    }
}
