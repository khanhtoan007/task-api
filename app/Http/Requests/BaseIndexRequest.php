<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Base validation rules for pagination, sorting, search
     * Override in child classes to add custom filters
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'sort_by' => 'sometimes|string|in:' . implode(',', $this->getAllowedSortFields()),
            'sort_order' => 'sometimes|string|in:asc,desc',
        ], $this->getCustomRules());
    }

    /**
     * Get allowed sort fields (override in child classes)
     *
     * @return array<string>
     */
    protected function getAllowedSortFields(): array
    {
        return ['created_at', 'updated_at'];
    }

    /**
     * Get custom validation rules (override in child classes)
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function getCustomRules(): array
    {
        return [];
    }

    /**
     * Get pagination parameters
     */
    public function getPagination(): array
    {
        return [
            'page' => (int) $this->input('page', 1),
            'per_page' => (int) $this->input('per_page', 15),
        ];
    }

    /**
     * Get filter parameters (override in child classes to add custom filters)
     */
    public function getFilters(): array
    {
        $filters = [];

        if ($this->has('search') && $this->filled('search')) {
            $filters['search'] = $this->input('search');
        }

        return array_merge($filters, $this->getCustomFilters());
    }

    /**
     * Get custom filters (override in child classes)
     */
    protected function getCustomFilters(): array
    {
        return [];
    }

    /**
     * Get sort parameters
     */
    public function getSorting(): array
    {
        return [
            'sort_by' => $this->input('sort_by', $this->getDefaultSortField()),
            'sort_order' => $this->input('sort_order', 'desc'),
        ];
    }

    /**
     * Get default sort field (override in child classes)
     */
    protected function getDefaultSortField(): string
    {
        return 'created_at';
    }
}

