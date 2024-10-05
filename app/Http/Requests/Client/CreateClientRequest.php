<?php

namespace App\Http\Requests\Client;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateClientRequest extends FormRequest
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
            'name' => 'string|required|max:255',
            'email' => 'string|required|email|max:255|unique:clients',
            'document' => 'string|required|max:14|unique:clients',
            'phone' => 'string|required|max:15',
            'address' => 'array|required',
            'address.street' => 'string|required|max:255',
            'address.number' => 'string|required|max:10',
            'address.complement' => 'string|nullable|max:255',
            'address.city' => 'string|required|max:255',
            'address.state' => 'string|required|max:2',
            'address.zip_code' => 'string|required|max:9',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json(['message' => $validator->errors()->first()], 422);
        throw new ValidationException($validator, $response);
    }
}
