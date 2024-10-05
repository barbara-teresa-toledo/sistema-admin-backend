<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string|required|max:255',
            'email' => 'string|required|email|max:255|unique:users',
            'password' => 'string|required|min:8|confirmed|max:255',
            'password_confirmation' => 'string|required|max:255'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json(['message' => $validator->errors()->first()], 422);
        throw new ValidationException($validator, $response);
    }
}
