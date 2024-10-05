<?php

namespace App\Http\Requests\ServiceOrder;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateServiceOrderRequest extends FormRequest
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
            'client_id' => 'integer|required|exists:clients,id',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json(['message' => $validator->errors()->first()], 422);
        throw new ValidationException($validator, $response);
    }
}
