<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateUserContestRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'sex' => ['required', 'in:male,female,other'],
            'phone' => ['required', 'string'],
            'dob' => ['required', 'date'],
            'contest' => ['required', 'uuid', 'exists:contests,id']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'User name is required',
            'sex.required' => 'User sex is required',
            'phone.required' => 'User phone is required',
            'dob.required' => 'User dob is required',
            'contests.required' => 'User contests is required',
        ];
    }
}
