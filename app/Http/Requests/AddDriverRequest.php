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
        return true; // Adjust based on your auth logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'postal_code' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'phone' => 'required|string',
            'is_created_by_typing' => 'boolean',
            'user_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
        ];
    }

    /**
     * Optional: custom messages
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'date_of_birth.required' => 'Date of birth is required.',
            'user_id.required' => 'User association is required.',
            'company_id.required' => 'Company association is required.',
        ];
    }
}
