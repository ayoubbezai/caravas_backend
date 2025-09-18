<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddDriverRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            // Driver fields
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:500',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'is_created_by_typing' => 'sometimes|boolean',
            'company_id' => 'required|exists:companies,id',

            // User fields (for creating user)
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Date of birth must be a valid date.',
            'address.required' => 'Address is required.',
            'postal_code.required' => 'Postal code is required.',
            'city.required' => 'City is required.',
            'country.required' => 'Country is required.',
            'phone.required' => 'Phone number is required.',
            'company_id.required' => 'Company association is required.',
            'company_id.exists' => 'The selected company does not exist.',

            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'Email has already been taken.',

            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'date_of_birth' => 'date of birth',
            'postal_code' => 'postal code',
            'company_id' => 'company',
            'password_confirmation' => 'password confirmation',
        ];
    }
}
