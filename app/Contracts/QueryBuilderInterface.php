<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

interface QueryBuilderInterface
{
    /**
     * Apply search to query builder
     *
     * @param  array<string>  $searchFields
     */
    public function applySearch(Builder $query, string $search, array $searchFields): Builder;

    /**
     * Apply filters to query builder
     *
     * @param  array<string, mixed>  $filters
     */
    public function applyFilters(Builder $query, array $filters, ?callable $customFilterCallback = null): Builder;

    /**
     * Apply sorting to query builder
     */
    public function applySorting(Builder $query, string $sortBy, string $sortOrder): Builder;

    /**
     * Apply pagination to query builder
     */
    public function applyPagination(Builder $query, int $perPage, int $page): LengthAwarePaginator;

    /**
     * Build complete query with all features
     *
     * @param  Builder|callable(): Builder  $query  Query builder instance or callable that returns Builder
     * @param  array<string, mixed>  $filters
     * @param  array<string>  $searchFields
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
    ): LengthAwarePaginator;
}
