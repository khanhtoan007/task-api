<?php

namespace App\Http\Requests;

use App\Http\Enums\ProjectRoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * @property string $user_id
 * @property ProjectRoleEnum $role
 */
final class AssignUserRequest extends FormRequest
{
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
            'user_id' => 'required|uuid|exists:users,id',
            'role' => ['required', new Enum(ProjectRoleEnum::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required',
            'user_id.uuid' => 'User ID must be a valid UUID',
            'user_id.exists' => 'User ID not found',
            'role.required' => 'Role is required',
            'role.enum' => 'Role must be a valid role',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'User ID',
            'role' => 'Role',
        ];
    }
}
