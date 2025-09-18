<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCompanyRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'name_of_company' => 'required|string|max:255|unique:companies,name_of_company',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'The first name field is required.',
            'first_name.string' => 'The first name must be a string.',
            'first_name.max' => 'The first name may not be greater than 255 characters.',

            'last_name.required' => 'The last name field is required.',
            'last_name.string' => 'The last name must be a string.',
            'last_name.max' => 'The last name may not be greater than 255 characters.',

            'phone_number.required' => 'The phone number field is required.',
            'phone_number.string' => 'The phone number must be a string.',
            'phone_number.max' => 'The phone number may not be greater than 20 characters.',

            'name_of_company.required' => 'The company name field is required.',
            'name_of_company.string' => 'The company name must be a string.',
            'name_of_company.max' => 'The company name may not be greater than 255 characters.',
            'name_of_company.unique' => 'A company with this name already exists.',

            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',

            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'phone_number' => 'phone number',
            'name_of_company' => 'company name',
            'email' => 'email',
            'password' => 'password',
        ];
    }
}
