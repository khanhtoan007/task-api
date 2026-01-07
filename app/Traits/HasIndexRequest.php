<?php

declare(strict_types=1);

namespace App\Traits;

trait HasIndexRequest
{
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
     * Get filter parameters
     */
    public function getFilters(): array
    {
        $filters = [];

        if ($this->has('search') && $this->filled('search')) {
            $filters['search'] = $this->input('search');
        }

        return $filters;
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
     * Base validation rules for pagination, sorting, searc
     */
    protected function getBaseRules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'sort_by' => 'sometimes|string|max:255',
            'sort_order' => 'sometimes|string|in:asc,desc',
        ];
    }

    /**
     * Get default sort field
     */
    protected function getDefaultSortField(): string
    {
        return 'created_at';
    }
}