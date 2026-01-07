<?php

namespace App\Http\Requests;

use App\Http\Enums\ProjectStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class ProjectRequest extends FormRequest
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
            'name' => 'required',
            'description' => 'required',
            'start_date' => 'required|date',
            'status' => ['required', new Enum(ProjectStatusEnum::class)],
            'end_date' => 'required|date|after:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Project name is required',
            'description.required' => 'Project description is required',
            'start_date.required' => 'Project start date is required',
            'status.required' => 'Project status is required',
            'status.enum' => 'Project status is not a valid status',
            'end_date.required' => 'Project end date is required',
            'start_date.date' => 'Project start date is not a valid date',
            'end_date.date' => 'Project end date is not a valid date',
            'end_date.after' => 'Project end date must be after start date',
        ];
    }
}
