<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "email"    => "required|email",
            "password" => "required|string|min:8|max:255",
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'email_required',
            'email.email'       => 'email_email',

            'password.required' => 'password_required',
            'password.string'   => 'password_string',
            'password.min'      => 'password_min',
            'password.max'      => 'password_max',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'errors'  => $errors
            ], 422)
        );
    }
}
