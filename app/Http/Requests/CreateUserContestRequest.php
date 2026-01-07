<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class CreateUserContestRequest extends FormRequest
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
            'email' => ['required', 'email'],
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
            'email.required' => 'User email is required',
            'email.email' => 'User email must be a valid email',
            'email.unique' => 'This user email has registered on this contest',
            'name.required' => 'User name is required',
            'sex.required' => 'User sex is required',
            'phone.required' => 'User phone is required',
            'dob.required' => 'User dob is required',
            'contests.required' => 'User contests is required',
        ];
    }
}
