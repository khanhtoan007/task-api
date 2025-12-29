<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class TaskIndexRequest extends BaseIndexRequest
{
    /**
     * @return array<string>
     */
    protected function getAllowedSortFields(): array
    {
        return ['title', 'status', 'created_at', 'updated_at'];
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function getCustomRules(): array
    {
        return [
            'status' => 'sometimes|string|max:50',
        ];
    }

    /**
     * Get custom filters from request
     */
    protected function getCustomFilters(): array
    {
        $filters = [];

        if ($this->has('status') && $this->filled('status')) {
            $filters['status'] = $this->input('status');
        }

        return $filters;
    }
}
