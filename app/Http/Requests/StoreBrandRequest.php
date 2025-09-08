<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $type = $this->route('type');

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('brands')->where('type', $type)
            ],
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            'picture' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => Str::upper($this->input('name')),
        ]);
    }
}
