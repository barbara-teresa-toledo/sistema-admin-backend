<?php

namespace App\Http\Requests\Client;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateClientRequest extends FormRequest
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
        //verify unique but is equal to the same document
        return [
            'name' => 'string|required|max:255',
            'email' => 'string|required|email|max:255|unique:clients,email,' . $this->route('client')->id,
            'document' => 'string|required|max:14|unique:clients,document,' . $this->route('client')->id,
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
