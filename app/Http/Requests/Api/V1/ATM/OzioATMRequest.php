<?php

namespace App\Http\Requests\Api\V1\ATM;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OzioATMRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'qty_1' => ['nullable', 'integer'],
            'qty_5' => ['nullable', 'integer'],
            'qty_10' => ['nullable', 'integer'],
            'qty_20' => ['nullable', 'integer'],
            'qty_50' => ['nullable', 'integer'],
            'qty_100' => ['nullable', 'integer'],
            'qty_200' => ['nullable', 'integer'],
            'qty_500' => ['nullable', 'integer']
        ];
    }
}
