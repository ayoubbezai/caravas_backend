<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddDriverLessonsRequest extends FormRequest
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
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'required|string|max:500',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'phone_email' => 'required|string|max:255',
            'license_number' => 'required|string|max:50',
            'license_category' => 'required|string|max:10|in:A,B,C,D,BE,CE,DE',
            'license_valid_until' => 'required|date|after:today',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'last_name.required' => 'Last name is required.',
            'last_name.string' => 'Last name must be a string.',
            'last_name.max' => 'Last name cannot exceed 255 characters.',

            'first_name.required' => 'First name is required.',
            'first_name.string' => 'First name must be a string.',
            'first_name.max' => 'First name cannot exceed 255 characters.',

            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Date of birth must be a valid date.',
            'date_of_birth.before' => 'Date of birth must be in the past.',

            'address.required' => 'Address is required.',
            'address.string' => 'Address must be a string.',
            'address.max' => 'Address cannot exceed 500 characters.',

            'postal_code.required' => 'Postal code is required.',
            'postal_code.string' => 'Postal code must be a string.',
            'postal_code.max' => 'Postal code cannot exceed 20 characters.',

            'city.required' => 'City is required.',
            'city.string' => 'City must be a string.',
            'city.max' => 'City cannot exceed 255 characters.',

            'country.required' => 'Country is required.',
            'country.string' => 'Country must be a string.',
            'country.max' => 'Country cannot exceed 255 characters.',

            'phone_email.required' => 'Phone or email is required.',
            'phone_email.string' => 'Phone or email must be a string.',
            'phone_email.max' => 'Phone or email cannot exceed 255 characters.',

            'license_number.required' => 'License number is required.',
            'license_number.string' => 'License number must be a string.',
            'license_number.max' => 'License number cannot exceed 50 characters.',

            'license_category.required' => 'License category is required.',
            'license_category.string' => 'License category must be a string.',
            'license_category.max' => 'License category cannot exceed 10 characters.',
            'license_category.in' => 'License category must be one of: A, B, C, D, BE, CE, DE.',

            'license_valid_until.required' => 'License expiration date is required.',
            'license_valid_until.date' => 'License expiration date must be a valid date.',
            'license_valid_until.after' => 'License expiration date must be in the future.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'last_name' => 'last name',
            'first_name' => 'first name',
            'date_of_birth' => 'date of birth',
            'address' => 'address',
            'postal_code' => 'postal code',
            'city' => 'city',
            'country' => 'country',
            'phone_email' => 'phone or email',
            'license_number' => 'license number',
            'license_category' => 'license category',
            'license_valid_until' => 'license expiration date',
        ];
    }
}
