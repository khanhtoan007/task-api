<?php

namespace App\Services;

use App\Contracts\QueryBuilderInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

final class QueryBuilder implements QueryBuilderInterface
{
    /**
     * Apply search to query builder
     */
    public function applySearch(Builder $query, string $search, array $searchFields): Builder
    {
        if (empty($searchFields)) {
            return $query;
        }

        return $query->where(function ($q) use ($search, $searchFields): void {
            foreach ($searchFields as $index => $field) {
                if ($index === 0) {
                    $q->where($field, 'like', "%{$search}%");
                } else {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            }
        });
    }

    /**
     * Apply filters to query builder
     */
    public function applyFilters(Builder $query, array $filters, ?callable $customFilterCallback = null): Builder
    {
        // Apply search if provided
        if (isset($filters['search']) && ! empty($filters['search'])) {
            $searchFields = $filters['search_fields'] ?? [];
            $query = $this->applySearch($query, $filters['search'], $searchFields);
        }

        // Apply custom filters via callback
        if ($customFilterCallback !== null) {
            $query = $customFilterCallback($query, $filters);
        }

        return $query;
    }

    /**
     * Apply sorting to query builder
     */
    public function applySorting(Builder $query, string $sortBy, string $sortOrder): Builder
    {
        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Apply pagination to query builder
     */
    public function applyPagination(Builder $query, int $perPage, int $page): LengthAwarePaginator
    {
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Build complete query with all features
     */
    public function build(
        Builder|callable $query,
        array $filters,
        array $searchFields,
        string $sortBy,
        string $sortOrder,
        int $perPage,
        int $page,
        ?callable $customFilterCallback = null
    ): LengthAwarePaginator {
        // Resolve query if it's a callable (for repositories, raw queries, etc.)
        if (is_callable($query)) {
            $query = $query();
        }

        if (! ($query instanceof Builder)) {
            throw new InvalidArgumentException('Query must be an instance of Builder or a callable that returns Builder');
        }

        // Add search_fields to filters for applyFilters method
        $filters['search_fields'] = $searchFields;

        $query = $this->applyFilters($query, $filters, $customFilterCallback);
        $query = $this->applySorting($query, $sortBy, $sortOrder);

        return $this->applyPagination($query, $perPage, $page);
    }
}
