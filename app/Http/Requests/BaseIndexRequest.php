<?php

namespace App\Http\Requests;

use App\Traits\HasIndexRequest;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Base IndexRequest chung cho tất cả list endpoints
 * Sử dụng trực tiếp, không cần tạo class con cho mỗi domain
 */
final class BaseIndexRequest extends FormRequest
{
    use HasIndexRequest;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules: chỉ base rules, custom rules sẽ được handle trong trait
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->getBaseRules();
    }
}
