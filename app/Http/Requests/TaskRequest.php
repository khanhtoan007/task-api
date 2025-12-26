<?php

namespace App\Http\Requests;

use App\Http\Enums\TaskStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

final class TaskRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => 'required|in:'.implode(',', TaskStatusEnum::cases()),
            'assigned_to' => 'required|uuid|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required',
            'description.required' => 'Description is required',
            'status.required' => 'Status is required',
            'assigned_to.required' => 'User assign to is required',
            'assigned_to.uuid' => 'User assign to is not a valid UUID',
            'assigned_to.exists' => 'User assign to is not found',
        ];
    }
}
